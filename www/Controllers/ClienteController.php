<?php

namespace Controllers;

require_once("Models/Database.php");
require_once("Config/Helpers.php");
use Models\ClienteModel;

class ClienteController
{
    private ClienteModel $model;

    private function bloquearAcesso(): void
    {
        if (function_exists('hasRole') && hasRole(['admin', 'profissional'])) {
            redirect('dashboard');
        }
    }

    public function __construct()
    {
        $this->model = new ClienteModel();
    }

    public function index(): void
    {
        $this->bloquearAcesso();
        $busca   = trim($_GET['busca'] ?? '');
        $clientes = $this->model->listar($busca);

        view('clientes/index', [
            'pagina'   => 'Clientes',
            'clientes' => $clientes,
            'busca'    => $busca,
        ]);
    }

    public function novo(): void
    {
        $this->bloquearAcesso();
        $msg = $_SESSION['msg'] ?? '';
        unset($_SESSION['msg']);
        view('clientes/form', ['pagina' => 'Novo Cliente', 'cliente' => [], 'msg' => $msg]);
    }

    public function salvar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('clientes');
        }
        $this->bloquearAcesso();

        $dados = [
            'cliente_nome'       => trim($_POST['cliente_nome'] ?? ''),
            'cliente_email'      => trim($_POST['cliente_email'] ?? ''),
            'cliente_telefone'   => trim($_POST['cliente_telefone'] ?? ''),
            'cliente_nascimento' => $_POST['cliente_nascimento'] ?: null,
            'cliente_obs'        => trim($_POST['cliente_obs'] ?? ''),
        ];

        if (empty($dados['cliente_nome'])) {
            $_SESSION['msg'] = msg('O nome do cliente é obrigatório.', 'danger');
            redirect('clientes/novo');
        }

        $this->model->salvar($dados);
        $_SESSION['msg'] = msg('Cliente cadastrado com sucesso!', 'success');
        redirect('clientes');
    }

    public function editar(int $id): void
    {
        $this->bloquearAcesso();
        $cliente = $this->model->buscarPorId($id);
        if (!$cliente) {
            redirect('clientes');
        }
        $msg = $_SESSION['msg'] ?? '';
        unset($_SESSION['msg']);
        view('clientes/form', ['pagina' => 'Editar Cliente', 'cliente' => $cliente, 'msg' => $msg]);
    }

    public function atualizar(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('clientes');
        }
        $this->bloquearAcesso();

        $dados = [
            'cliente_nome'       => trim($_POST['cliente_nome'] ?? ''),
            'cliente_email'      => trim($_POST['cliente_email'] ?? ''),
            'cliente_telefone'   => trim($_POST['cliente_telefone'] ?? ''),
            'cliente_nascimento' => $_POST['cliente_nascimento'] ?: null,
            'cliente_obs'        => trim($_POST['cliente_obs'] ?? ''),
        ];

        $this->model->atualizar($id, $dados);
        $_SESSION['msg'] = msg('Cliente atualizado com sucesso!', 'success');
        redirect('clientes');
    }

    public function excluir(int $id): void
    {
        $this->bloquearAcesso();
        $this->model->remover($id);
        $_SESSION['msg'] = msg('Cliente removido.', 'warning');
        redirect('clientes');
    }
}
