<?php

namespace Controllers;

require_once("Models/Database.php");
require_once("Config/Helpers.php");

use Models\AgendamentoModel;
use Models\ClienteModel;
use Models\ServicoModel;
use Models\UsuarioModel;

class ClienteAgendamentoController
{
    private AgendamentoModel $agendamentoModel;
    private ClienteModel $clienteModel;
    private ServicoModel $servicoModel;
    private UsuarioModel $usuarioModel;

    public function __construct()
    {
        $this->agendamentoModel = new AgendamentoModel();
        $this->clienteModel     = new ClienteModel();
        $this->servicoModel     = new ServicoModel();
        $this->usuarioModel     = new UsuarioModel();
    }

    private function garantirPerfilCliente(): void
    {
        if (function_exists('requireRole')) {
            requireRole(['cliente']);
        }
    }

    private function clienteLogado(): array
    {
        $this->garantirPerfilCliente();

        $usuarioId = (int)($_SESSION['usuario_id'] ?? 0);
        $email     = $_SESSION['usuario_email'] ?? null;

        if (!$usuarioId) {
            redirect('login');
        }

        $cliente = $this->clienteModel->buscarPorUsuarioId($usuarioId, $email);
        if (!$cliente) {
            $_SESSION['msg'] = msg('Não encontramos um cadastro de cliente vinculado à sua conta. Entre em contato com o atendimento.', 'danger');
            redirect('dashboard');
        }

        return $cliente;
    }

    private function montarDataHora(?string $data, ?string $hora): ?\DateTime
    {
        if (empty($data) || empty($hora)) {
            return null;
        }

        try {
            return new \DateTime($data . ' ' . $hora);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function index(): void
    {
        $msg = $_SESSION['msg'] ?? '';
        unset($_SESSION['msg']);

        $cliente   = $this->clienteLogado();
        $proximos  = $this->agendamentoModel->listarProximosPorCliente((int)$cliente['cliente_id']);
        $historico = $this->agendamentoModel->listarHistoricoPorCliente((int)$cliente['cliente_id']);

        view('cliente/agendamentos/index', [
            'pagina'    => 'Meus Agendamentos',
            'cliente'   => $cliente,
            'proximos'  => $proximos,
            'historico' => $historico,
            'msg'       => $msg,
        ]);
    }

    public function novo(): void
    {
        $cliente = $this->clienteLogado();

        $msg = $_SESSION['msg'] ?? '';
        unset($_SESSION['msg']);

        view('cliente/agendamentos/form', [
            'pagina'        => 'Novo Agendamento',
            'cliente'       => $cliente,
            'servicos'      => $this->servicoModel->listarAtivos(),
            'profissionais' => $this->usuarioModel->listarPorTipo('profissional'),
            'msg'           => $msg,
            'action'        => base_url('cliente/agendamentos/salvar'),
            'agendamento'   => [
                'servico_id'        => null,
                'usuario_id'        => null,
                'agendamento_data'  => date('Y-m-d'),
                'agendamento_hora'  => '08:00',
                'agendamento_obs'   => '',
            ],
        ]);
    }

    public function salvar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('cliente/agendamentos');
        }

        $cliente = $this->clienteLogado();

        $dados = [
            'cliente_id'         => (int)$cliente['cliente_id'],
            'servico_id'         => (int)($_POST['servico_id'] ?? 0),
            'usuario_id'         => (int)($_POST['usuario_id'] ?? 0),
            'agendamento_data'   => $_POST['agendamento_data'] ?? '',
            'agendamento_hora'   => $_POST['agendamento_hora'] ?? '',
            'agendamento_obs'    => trim($_POST['agendamento_obs'] ?? ''),
            'agendamento_status' => 'aguardando',
        ];

        if (!$dados['servico_id'] || !$dados['usuario_id'] || empty($dados['agendamento_data']) || empty($dados['agendamento_hora'])) {
            $_SESSION['msg'] = msg('Preencha todos os campos obrigatórios.', 'danger');
            redirect('cliente/agendamentos/novo');
        }

        $profissional = $this->usuarioModel->buscarPorId($dados['usuario_id']);
        if (!$profissional || ($profissional['usuario_perfil'] ?? '') !== 'profissional') {
            $_SESSION['msg'] = msg('Selecione um profissional válido.', 'danger');
            redirect('cliente/agendamentos/novo');
        }

        $dataHora = $this->montarDataHora($dados['agendamento_data'], $dados['agendamento_hora']);
        if (!$dataHora || $dataHora <= new \DateTime()) {
            $_SESSION['msg'] = msg('Escolha uma data e horário futuros.', 'danger');
            redirect('cliente/agendamentos/novo');
        }

        $this->agendamentoModel->salvar($dados);
        $_SESSION['msg'] = msg('Agendamento criado com sucesso!', 'success');
        redirect('cliente/agendamentos');
    }

    public function editar(int $id): void
    {
        $cliente = $this->clienteLogado();

        $msg = $_SESSION['msg'] ?? '';
        unset($_SESSION['msg']);

        $agendamento = $this->agendamentoModel->buscarPorIdDoCliente($id, (int)$cliente['cliente_id']);
        if (!$agendamento) {
            $_SESSION['msg'] = msg('Agendamento não encontrado.', 'danger');
            redirect('cliente/agendamentos');
        }

        view('cliente/agendamentos/form', [
            'pagina'        => 'Editar Agendamento',
            'cliente'       => $cliente,
            'servicos'      => $this->servicoModel->listarAtivos(),
            'profissionais' => $this->usuarioModel->listarPorTipo('profissional'),
            'msg'           => $msg,
            'action'        => base_url("cliente/agendamentos/{$id}/atualizar"),
            'agendamento'   => $agendamento,
        ]);
    }

    public function atualizar(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('cliente/agendamentos');
        }

        $cliente = $this->clienteLogado();
        $agendamento = $this->agendamentoModel->buscarPorIdDoCliente($id, (int)$cliente['cliente_id']);

        if (!$agendamento) {
            $_SESSION['msg'] = msg('Agendamento não encontrado.', 'danger');
            redirect('cliente/agendamentos');
        }

        $dados = [
            'servico_id'       => (int)($_POST['servico_id'] ?? 0),
            'usuario_id'       => (int)($_POST['usuario_id'] ?? 0),
            'agendamento_data' => $_POST['agendamento_data'] ?? '',
            'agendamento_hora' => $_POST['agendamento_hora'] ?? '',
            'agendamento_obs'  => trim($_POST['agendamento_obs'] ?? ''),
        ];

        if (!$dados['servico_id'] || !$dados['usuario_id'] || empty($dados['agendamento_data']) || empty($dados['agendamento_hora'])) {
            $_SESSION['msg'] = msg('Preencha todos os campos obrigatórios.', 'danger');
            redirect("cliente/agendamentos/{$id}/editar");
        }

        $profissional = $this->usuarioModel->buscarPorId($dados['usuario_id']);
        if (!$profissional || ($profissional['usuario_perfil'] ?? '') !== 'profissional') {
            $_SESSION['msg'] = msg('Selecione um profissional válido.', 'danger');
            redirect("cliente/agendamentos/{$id}/editar");
        }

        $novaDataHora = $this->montarDataHora($dados['agendamento_data'], $dados['agendamento_hora']);
        if (!$novaDataHora || $novaDataHora <= new \DateTime()) {
            $_SESSION['msg'] = msg('Escolha uma data e horário futuros.', 'danger');
            redirect("cliente/agendamentos/{$id}/editar");
        }

        $dataHoraAtual = $this->montarDataHora($agendamento['agendamento_data'] ?? null, $agendamento['agendamento_hora'] ?? null);
        $limite         = new \DateTime('+12 hours');

        $alterouData = $dados['agendamento_data'] !== ($agendamento['agendamento_data'] ?? '')
            || $dados['agendamento_hora'] !== ($agendamento['agendamento_hora'] ?? '');

        if ($alterouData && $dataHoraAtual && $dataHoraAtual < $limite) {
            $_SESSION['msg'] = msg('Remarcações só são permitidas com pelo menos 12 horas de antecedência.', 'warning');
            redirect("cliente/agendamentos/{$id}/editar");
        }

        $this->agendamentoModel->atualizar($id, $dados);
        $_SESSION['msg'] = msg('Agendamento atualizado com sucesso!', 'success');
        redirect('cliente/agendamentos');
    }

    public function cancelar(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('cliente/agendamentos');
        }

        $cliente = $this->clienteLogado();
        $agendamento = $this->agendamentoModel->buscarPorIdDoCliente($id, (int)$cliente['cliente_id']);

        if (!$agendamento) {
            $_SESSION['msg'] = msg('Agendamento não encontrado.', 'danger');
            redirect('cliente/agendamentos');
        }

        if (($agendamento['agendamento_status'] ?? '') === 'cancelado') {
            $_SESSION['msg'] = msg('Este agendamento já foi cancelado anteriormente.', 'info');
            redirect('cliente/agendamentos');
        }

        $this->agendamentoModel->atualizarStatus($id, 'cancelado');
        $_SESSION['msg'] = msg('Agendamento cancelado com sucesso.', 'warning');
        redirect('cliente/agendamentos');
    }
}
