<?php

namespace Controllers;

require_once("Models/Database.php");
require_once("Config/Helpers.php");
use Models\UsuarioModel;

class AuthController
{
    private UsuarioModel $model;

    public function __construct()
    {
        $this->model = new UsuarioModel();
    }

    /**
     * Exibe a tela de login
     */
    public function login(): void
    {
        $erro = $_SESSION['login_erro'] ?? '';
        unset($_SESSION['login_erro']);
        include("Views/auth/login.php");
    }

    /**
     * Processa o formulário de login
     */
    public function authenticate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('login');
        }

        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if (empty($email) || empty($senha)) {
            $_SESSION['login_erro'] = 'Preencha e-mail e senha.';
            redirect('login');
        }

        $usuario = $this->model->buscarPorEmail($email);

        if (!$usuario || !password_verify($senha, $usuario['usuario_senha'])) {
            $_SESSION['login_erro'] = 'E-mail ou senha inválidos.';
            redirect('login');
        }

        // Inicia a sessão do usuário
        $_SESSION['usuario_id']     = $usuario['usuario_id'];
        $_SESSION['usuario_nome']   = $usuario['usuario_nome'];
        $_SESSION['usuario_perfil'] = $usuario['usuario_perfil'];

        redirect('dashboard');
    }

    /**
     * Encerra a sessão
     */
    public function logout(): void
    {
        session_destroy();
        redirect('login');
    }
}
