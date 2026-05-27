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
    private ClienteModel $clienteModel;
    private ServicoModel $servicoModel;
    private UsuarioModel $usuarioModel;

    public function __construct()
    {
        $this->model = new AgendamentoModel();
        $this->clienteModel = new ClienteModel();
        $this->servicoModel = new ServicoModel();
        $this->usuarioModel = new UsuarioModel();
    }

    public function index(): void
    {
        $data = $_GET['data'] ?? date('Y-m-d');
        $msg = $_SESSION['msg'] ?? '';
        unset($_SESSION['msg']);

        // Datas de navegação anterior/próxima
        $dataAnterior = date('Y-m-d', strtotime($data . ' -1 day'));
        $dataProxima = date('Y-m-d', strtotime($data . ' +1 day'));

        view('agendamentos/index', [
            'pagina' => 'Agendamentos',
            'agendamentos' => $this->model->listarPorData($data),
            'data' => $data,
            'dataAnterior' => $dataAnterior,
            'dataProxima' => $dataProxima,
            'msg' => $msg,
        ]);
    }

    public function novo(): void
    {
        $msg = $_SESSION['msg'] ?? '';
        unset($_SESSION['msg']);

        view('agendamentos/form', [
            'pagina' => 'Novo Agendamento',
            'clientes' => $this->clienteModel->listar(),
            'servicos' => $this->servicoModel->listarAtivos(),
            'usuarios' => $this->usuarioModel->listar(),
            'msg' => $msg,
        ]);
    }

    public function salvar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('agendamentos');
        }

        $dados = [
            'cliente_id' => (int) ($_POST['cliente_id'] ?? 0),
            'servico_id' => (int) ($_POST['servico_id'] ?? 0),
            ' id' => (int) ($_POST[' id'] ?? 0),
            'agendamento_data' => $_POST['agendamento_data'] ?? '',
            'agendamento_hora' => $_POST['agendamento_hora'] ?? '',
            'agendamento_obs' => trim($_POST['agendamento_obs'] ?? ''),
            'agendamento_status' => 'aguardando',
        ];

        if (
            !$dados['cliente_id'] || !$dados['servico_id'] || !$dados[' id']
            || empty($dados['agendamento_data']) || empty($dados['agendamento_hora'])
        ) {
            $_SESSION['msg'] = msg('Preencha todos os campos obrigatórios.', 'danger');
            redirect('agendamentos/novo');
        }

        $this->model->salvar($dados);
        $_SESSION['msg'] = msg('Agendamento criado com sucesso!', 'success');
        redirect('agendamentos?data=' . $dados['agendamento_data']);
    }

    public function status(int $id): void
    {
        $novoStatus = $_POST['status'] ?? '';
        $statusValidos = ['aguardando', 'confirmado', 'em_andamento', 'concluido', 'cancelado'];

        if (in_array($novoStatus, $statusValidos)) {
            $this->model->atualizarStatus($id, $novoStatus);
            $_SESSION['msg'] = msg('Status atualizado com sucesso!', 'success');
        }

        $data = $_POST['data'] ?? date('Y-m-d');
        redirect('agendamentos?data=' . $data);
    }

    public function excluir(int $id): void
    {
        $this->model->remover($id);
        $_SESSION['msg'] = msg('Agendamento removido.', 'warning');
        redirect('agendamentos');
    }
}
