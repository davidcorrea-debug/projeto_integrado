<?php

namespace Controllers;

require_once("Models/Database.php");
require_once("Config/Helpers.php");
require_once("Config/Security.php");
use Models\UsuarioModel;

class ProfissionalController
{
    private UsuarioModel $model;

    public function __construct()
    {
        $this->model = new UsuarioModel();
    }

    public function index(): void
    {
        if (function_exists('requireRole')) requireRole(['admin']);

        $busca = trim($_GET['busca'] ?? '');
        $profissionais = $this->model->listarPorTipo('profissional');
        if ($busca !== '') {
            $profissionais = array_values(array_filter($profissionais, function($u) use ($busca) {
                $b = mb_strtolower($busca);
                return (str_contains(mb_strtolower($u['usuario_nome']), $b)
                    || str_contains(mb_strtolower($u['usuario_email']), $b));
            }));
        }

        view('profissionais/index', [
            'pagina'        => 'Profissionais',
            'profissionais' => $profissionais,
            'busca'         => $busca,
        ]);
    }

    public function novo(): void
    {
        if (function_exists('requireRole')) requireRole(['admin']);
        $msg = $_SESSION['msg'] ?? '';
        unset($_SESSION['msg']);
        view('profissionais/form', [
            'pagina' => 'Novo Profissional',
            'usuario' => [],
            'msg' => $msg,
        ]);
    }

    public function salvar(): void
    {
        if (function_exists('requireRole')) requireRole(['admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('profissionais');
        }

        $nome  = trim($_POST['usuario_nome'] ?? '');
        $email = trim($_POST['usuario_email'] ?? '');
        $senha = $_POST['usuario_senha'] ?? '';
        $conf  = $_POST['confirmar_senha'] ?? '';

        if ($nome === '' || $email === '' || $senha === '' || $conf === '') {
            $_SESSION['msg'] = msg('Preencha todos os campos obrigatórios.', 'danger');
            redirect('profissionais/novo');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['msg'] = msg('Informe um e-mail válido.', 'danger');
            redirect('profissionais/novo');
        }
        if ($senha !== $conf) {
            $_SESSION['msg'] = msg('As senhas não conferem.', 'danger');
            redirect('profissionais/novo');
        }
        if (strlen($senha) < 6) {
            $_SESSION['msg'] = msg('A senha deve ter pelo menos 6 caracteres.', 'danger');
            redirect('profissionais/novo');
        }

        $dados = [
            'usuario_nome'   => $nome,
            'usuario_email'  => $email,
            'usuario_senha'  => $senha, // será hasheada no model
            'usuario_perfil' => 'profissional',
            'usuario_ativo'  => 1,
        ];

        try {
            $this->model->salvar($dados);
            $_SESSION['msg'] = msg('Profissional cadastrado com sucesso!', 'success');
            redirect('profissionais');
        } catch (\Throwable $e) {
            // Provavelmente e-mail duplicado (UNIQUE)
            $_SESSION['msg'] = msg('Não foi possível cadastrar. Verifique se o e-mail já está em uso.', 'danger');
            redirect('profissionais/novo');
        }
    }
}
