<?php

namespace Controllers;

require_once("Models/Database.php");
require_once("Config/Helpers.php");
use Models\AgendamentoModel;
use Models\ClienteModel;
use Models\ServicoModel;
use Models\UsuarioModel;

class AgendamentoController
{
    private AgendamentoModel $model;
    private ClienteModel     $clienteModel;
    private ServicoModel     $servicoModel;
    private UsuarioModel     $usuarioModel;

    public function __construct()
    {
        $this->model        = new AgendamentoModel();
        $this->clienteModel = new ClienteModel();
        $this->servicoModel = new ServicoModel();
        $this->usuarioModel = new UsuarioModel();
    }

    public function index(): void
    {
        if (function_exists('hasRole') && hasRole(['cliente'])) {
            redirect('cliente/agendamentos');
        }
        $data  = $_GET['data'] ?? date('Y-m-d');
        $msg   = $_SESSION['msg'] ?? '';
        unset($_SESSION['msg']);

        // Datas de navegação anterior/próxima
        $dataAnterior = date('Y-m-d', strtotime($data . ' -1 day'));
        $dataProxima  = date('Y-m-d', strtotime($data . ' +1 day'));

        // RBAC: filtrar lista por perfil
        if (function_exists('hasRole') && function_exists('currentUserId')) {
            if (hasRole(['admin'])) {
                $agendamentos = $this->model->listarPorData($data);
            } elseif (hasRole(['profissional'])) {
                $agendamentos = $this->model->listarPorDataProfissional($data, currentUserId());
            } else { // cliente
                $agendamentos = $this->model->listarPorDataCliente($data, currentUserId());
            }
        } else {
            $agendamentos = $this->model->listarPorData($data);
        }

        view('agendamentos/index', [
            'pagina'        => 'Agendamentos',
            'agendamentos'  => $agendamentos,
            'data'          => $data,
            'dataAnterior'  => $dataAnterior,
            'dataProxima'   => $dataProxima,
            'msg'           => $msg,
            'role'          => $_SESSION['usuario_perfil'] ?? '',
        ]);
    }

    public function novo(): void
    {
        if (function_exists('hasRole') && hasRole(['cliente'])) {
            redirect('cliente/agendamentos/novo');
        }
        $msg = $_SESSION['msg'] ?? '';
        unset($_SESSION['msg']);
        // Se for cliente, restringe a si mesmo na lista de clientes
        $clientes = $this->clienteModel->listar();
        if (function_exists('hasRole') && hasRole(['cliente'])) {
            $email = $_SESSION['usuario_email'] ?? '';
            $cli = $this->clienteModel->buscarPorUsuarioId((int)($_SESSION['usuario_id'] ?? 0), $email);
            $clientes = $cli ? [$cli] : [];
            if (!$cli) {
                $_SESSION['msg'] = msg('Seu usuário não está vinculado a um cliente pelo mesmo e-mail. Contate o suporte.', 'danger');
            }
        }

        view('agendamentos/form', [
            'pagina'    => 'Novo Agendamento',
            'clientes'  => $clientes,
            'servicos'  => $this->servicoModel->listarAtivos(),
            'usuarios'  => (method_exists($this->usuarioModel, 'listarPorTipo') ? $this->usuarioModel->listarPorTipo('profissional') : $this->usuarioModel->listar()),
            'msg'       => $msg,
        ]);
    }

    public function salvar(): void
    {
        if (function_exists('hasRole') && hasRole(['cliente'])) {
            redirect('cliente/agendamentos');
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('agendamentos');
        }
        // Regra: admin/profissional podem criar livremente; cliente pode criar apenas para si
        $isCliente = function_exists('hasRole') && hasRole(['cliente']);
        if (!$isCliente && function_exists('requireRole')) requireRole(['admin','profissional']);

        $dados = [
            'cliente_id'          => (int) ($_POST['cliente_id'] ?? 0),
            'servico_id'          => (int) ($_POST['servico_id'] ?? 0),
            'usuario_id'          => (int) ($_POST['usuario_id'] ?? 0),
            'agendamento_data'    => $_POST['agendamento_data'] ?? '',
            'agendamento_hora'    => $_POST['agendamento_hora'] ?? '',
            'agendamento_obs'     => trim($_POST['agendamento_obs'] ?? ''),
            'agendamento_status'  => 'aguardando',
        ];

        // Se for cliente, força o cliente_id para o do usuário logado e valida profissional
        if ($isCliente) {
            $email = $_SESSION['usuario_email'] ?? '';
            $cli = $this->clienteModel->buscarPorUsuarioId((int)($_SESSION['usuario_id'] ?? 0), $email);
            if (!$cli) {
                $_SESSION['msg'] = msg('Seu usuário não está vinculado a um cliente pelo mesmo e-mail. Contate o suporte.', 'danger');
                redirect('agendamentos/novo');
            }
            $dados['cliente_id'] = (int)$cli['cliente_id'];
            // valida se usuario_id escolhido é profissional
            $prof = $this->usuarioModel->buscarPorId((int)$dados['usuario_id']);
            if (!$prof || ($prof['usuario_perfil'] ?? '') !== 'profissional') {
                $_SESSION['msg'] = msg('Selecione um profissional válido.', 'danger');
                redirect('agendamentos/novo');
            }
        }

        if (!$dados['cliente_id'] || !$dados['servico_id'] || !$dados['usuario_id']
            || empty($dados['agendamento_data']) || empty($dados['agendamento_hora'])) {
            $_SESSION['msg'] = msg('Preencha todos os campos obrigatórios.', 'danger');
            redirect('agendamentos/novo');
        }

        if ($this->model->existeConflitoHorario($dados['usuario_id'], $dados['agendamento_data'], $dados['agendamento_hora'])) {
            $_SESSION['msg'] = msg('Esse profissional já possui um agendamento neste horário.', 'danger');
            redirect('agendamentos/novo');
        }

        $this->model->salvar($dados);
        $_SESSION['msg'] = msg('Agendamento criado com sucesso!', 'success');
        redirect('agendamentos?data=' . $dados['agendamento_data']);
    }

    public function status(int $id): void
    {
        if (function_exists('requireRole')) requireRole(['admin','profissional']);
        $novoStatus = $_POST['status'] ?? '';
        $statusValidos = ['aguardando', 'confirmado', 'em_andamento', 'concluido', 'cancelado'];

        if (!in_array($novoStatus, $statusValidos)) {
            $_SESSION['msg'] = msg('Status inválido informado.', 'danger');
            redirect('agendamentos');
        }

        $agendamento = $this->model->buscarPorId($id);
        if (!$agendamento) {
            $_SESSION['msg'] = msg('Agendamento não encontrado.', 'danger');
            redirect('agendamentos');
        }

        if ($novoStatus === 'cancelado') {
            $data = $agendamento['agendamento_data'] ?? null;
            $hora = $agendamento['agendamento_hora'] ?? null;
            if (!empty($data) && !empty($hora)) {
                try {
                    $dataHora = new \DateTime($data . ' ' . $hora);
                    $limite   = new \DateTime('+2 hours');
                    if ($dataHora < $limite) {
                        $_SESSION['msg'] = msg('Cancelamentos só são permitidos com pelo menos 2 horas de antecedência.', 'warning');
                        $dataRedirect = $_POST['data'] ?? $data;
                        redirect('agendamentos?data=' . $dataRedirect);
                    }
                } catch (\Throwable $e) {
                    // Se não conseguir montar a data, seguimos adiante para evitar bloquear erroneamente
                }
            }
        }

        if (in_array($novoStatus, $statusValidos)) {
            $this->model->atualizarStatus($id, $novoStatus);
            $_SESSION['msg'] = msg('Status atualizado com sucesso!', 'success');
        }

        $data = $_POST['data'] ?? date('Y-m-d');
        redirect('agendamentos?data=' . $data);
    }

    public function excluir(int $id): void
    {
        if (function_exists('requireRole')) requireRole(['admin','profissional']);
        $this->model->remover($id);
        $_SESSION['msg'] = msg('Agendamento removido.', 'warning');
        redirect('agendamentos');
    }
}
