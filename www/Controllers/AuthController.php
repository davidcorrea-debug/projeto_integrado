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
        error_log('[AUTH] authenticate start method=' . ($_SERVER['REQUEST_METHOD'] ?? '') . ' uri=' . ($_SERVER['REQUEST_URI'] ?? ''));
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log('[AUTH] invalid method, redirecting to login');
            redirect('login');
        }

        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        error_log('[AUTH] received email=' . $email . ' senha_len=' . strlen($senha));

        if (empty($email) || empty($senha)) {
            error_log('[AUTH] missing email or password');
            $_SESSION['login_erro'] = 'Preencha e-mail e senha.';
            redirect('login');
        }

        $usuario = $this->model->buscarPorEmail($email);
        error_log('[AUTH] lookup result found=' . ($usuario ? '1' : '0') . ($usuario ? (' keys=' . implode(',', array_keys($usuario))) : ''));

        if (!$usuario || !password_verify($senha, $usuario['usuario_senha'])) {
            error_log('[AUTH] invalid credentials for email=' . $email);
            $_SESSION['login_erro'] = 'E-mail ou senha inválidos.';
            redirect('login');
        }

        // Inicia a sessão do usuário
        $_SESSION['usuario_id']     = $usuario['usuario_id'];
        $_SESSION['usuario_nome']   = $usuario['usuario_nome'];
        $_SESSION['usuario_perfil'] = $usuario['usuario_perfil'];

        error_log('[AUTH] login success id=' . $_SESSION['usuario_id'] . ' nome=' . $_SESSION['usuario_nome'] . ' perfil=' . $_SESSION['usuario_perfil']);

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
