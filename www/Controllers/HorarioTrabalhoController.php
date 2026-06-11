<?php

namespace Controllers;

require_once("Models/Database.php");
require_once("Config/Helpers.php");
require_once("Config/Security.php");

use Models\HorarioTrabalhoModel;
use Models\UsuarioModel;

class HorarioTrabalhoController
{
    private HorarioTrabalhoModel $horarioModel;
    private UsuarioModel $usuarioModel;

    /** @var array<int, string> */
    private array $diasSemana = [
        0 => 'Domingo',
        1 => 'Segunda-feira',
        2 => 'Terça-feira',
        3 => 'Quarta-feira',
        4 => 'Quinta-feira',
        5 => 'Sexta-feira',
        6 => 'Sábado',
    ];

    public function __construct()
    {
        $this->horarioModel = new HorarioTrabalhoModel();
        $this->usuarioModel = new UsuarioModel();
    }

    public function editar(int $usuarioId): void
    {
        if (function_exists('requireRole')) {
            requireRole(['admin']);
        }

        $profissional = $this->carregarProfissional($usuarioId, true);
        if (!$profissional) {
            redirect('profissionais');
        }

        $msg = $_SESSION['msg'] ?? '';
        unset($_SESSION['msg']);

        view('profissionais/horarios', [
            'pagina'       => 'Horários de trabalho',
            'profissional' => $profissional,
            'horarios'     => $this->normalizarHorarios($usuarioId),
            'diasSemana'   => $this->diasSemana,
            'action'       => base_url("profissionais/{$usuarioId}/horarios/salvar"),
            'msg'          => $msg,
            'voltar'       => base_url('profissionais'),
        ]);
    }

    public function salvar(int $usuarioId): void
    {
        if (function_exists('requireRole')) {
            requireRole(['admin']);
        }

        $this->processarFormulario($usuarioId, "profissionais/{$usuarioId}/horarios", true);
    }

    public function editarProprio(): void
    {
        if (function_exists('requireRole')) {
            requireRole(['profissional']);
        }

        $usuarioId = $this->usuarioLogadoId();
        if (!$usuarioId) {
            redirect('dashboard');
        }

        $profissional = $this->carregarProfissional($usuarioId, false);
        $msg = $_SESSION['msg'] ?? '';
        unset($_SESSION['msg']);

        view('profissionais/horarios', [
            'pagina'       => 'Meu expediente',
            'profissional' => $profissional,
            'horarios'     => $this->normalizarHorarios($usuarioId),
            'diasSemana'   => $this->diasSemana,
            'action'       => base_url('profissional/horarios/salvar'),
            'msg'          => $msg,
            'voltar'       => base_url('dashboard'),
        ]);
    }

    public function salvarProprio(): void
    {
        if (function_exists('requireRole')) {
            requireRole(['profissional']);
        }

        $usuarioId = $this->usuarioLogadoId();
        if (!$usuarioId) {
            redirect('dashboard');
        }

        $this->processarFormulario($usuarioId, 'profissional/horarios', false);
    }

    private function processarFormulario(int $usuarioId, string $redirectPath, bool $validarPerfil): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect($redirectPath);
        }

        if ($validarPerfil) {
            $profissional = $this->carregarProfissional($usuarioId, true);
            if (!$profissional) {
                redirect('profissionais');
            }
        }

        $dias = $_POST['dias'] ?? [];
        $erros = [];

        foreach ($this->diasSemana as $indice => $nome) {
            $item = $dias[$indice] ?? [];
            $ativo = isset($item['ativo']);
            $inicio = trim($item['inicio'] ?? '');
            $fim = trim($item['fim'] ?? '');

            if ($ativo) {
                if (!$this->horaValida($inicio) || !$this->horaValida($fim)) {
                    $erros[] = "Informe horários válidos (HH:MM) para {$nome}.";
                    continue;
                }

                if (!$this->horaInicialMenor($inicio, $fim)) {
                    $erros[] = "Em {$nome}, o horário final deve ser maior que o inicial.";
                }
            }
        }

        if (!empty($erros)) {
            $_SESSION['msg'] = msg(implode(' ', $erros), 'danger');
            redirect($redirectPath);
        }

        foreach ($this->diasSemana as $indice => $nome) {
            $item = $dias[$indice] ?? [];
            $ativo = isset($item['ativo']);
            $inicio = $this->normalizarHoraBanco($item['inicio'] ?? '08:00');
            $fim    = $this->normalizarHoraBanco($item['fim'] ?? '20:00');

            if (!$ativo) {
                $inicio = '00:00:00';
                $fim    = '00:00:00';
            }

            $this->horarioModel->salvarOuAtualizar($usuarioId, $indice, $inicio, $fim, $ativo);
        }

        $_SESSION['msg'] = msg('Horários de trabalho atualizados com sucesso!', 'success');
        redirect($redirectPath);
    }

    private function normalizarHorarios(int $usuarioId): array
    {
        $existentes = $this->horarioModel->listarPorUsuario($usuarioId);
        $resultado = [];

        foreach ($this->diasSemana as $indice => $nome) {
            $registro = $existentes[$indice] ?? null;
            $resultado[$indice] = [
                'dia'    => $nome,
                'ativo'  => (int)($registro['ativo'] ?? 1) === 1,
                'inicio' => $this->formatarHoraVisual($registro['hora_inicio'] ?? '08:00:00'),
                'fim'    => $this->formatarHoraVisual($registro['hora_fim'] ?? '20:00:00'),
            ];
        }

        return $resultado;
    }

    private function formatarHoraVisual(string $hora): string
    {
        return substr($hora, 0, 5);
    }

    private function horaValida(?string $hora): bool
    {
        if (!is_string($hora)) {
            return false;
        }

        return preg_match('/^\d{2}:\d{2}$/', $hora) === 1;
    }

    private function horaInicialMenor(string $inicio, string $fim): bool
    {
        return strtotime($inicio) < strtotime($fim);
    }

    private function normalizarHoraBanco(string $hora): string
    {
        if (!$this->horaValida($hora)) {
            return '00:00:00';
        }

        return $hora . ':00';
    }

    private function usuarioLogadoId(): int
    {
        if (function_exists('currentUserId')) {
            return (int)(currentUserId() ?? 0);
        }

        return (int)($_SESSION['usuario_id'] ?? 0);
    }

    private function carregarProfissional(int $usuarioId, bool $exigirProfissional): ?array
    {
        $usuario = $this->usuarioModel->buscarPorId($usuarioId);
        if (!$usuario) {
            $_SESSION['msg'] = msg('Profissional não encontrado.', 'danger');
            return null;
        }

        if ($exigirProfissional && ($usuario['usuario_perfil'] ?? '') !== 'profissional') {
            $_SESSION['msg'] = msg('Usuário informado não é um profissional.', 'danger');
            return null;
        }

        return $usuario;
    }
}
