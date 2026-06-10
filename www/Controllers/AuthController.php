<?php

namespace Controllers;

require_once("Models/Database.php");
require_once("Config/Helpers.php");
require_once("Models/EstabelecimentoModel.php");
use Models\UsuarioModel;
use Models\PasswordResetModel;
use Models\EstabelecimentoModel;

class AuthController
{
    private UsuarioModel $model;
    private EstabelecimentoModel $estabelecimentoModel;

    public function __construct()
    {
        $this->model = new UsuarioModel();
        $this->estabelecimentoModel = new EstabelecimentoModel();
    }

    /**
     * Exibe a tela de login
     */
    public function login(): void
    {
        $this->garantirProfissionalDemo();

        $erro    = $_SESSION['login_erro'] ?? '';
        $sucesso = $_SESSION['login_sucesso'] ?? '';
        $estabelecimento = $this->estabelecimentoModel->obter();
        unset($_SESSION['login_erro'], $_SESSION['login_sucesso']);
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
        $_SESSION['usuario_email']  = $usuario['usuario_email'];

        error_log('[AUTH] login success id=' . $_SESSION['usuario_id'] . ' nome=' . $_SESSION['usuario_nome'] . ' perfil=' . $_SESSION['usuario_perfil']);

        $destino = ($_SESSION['usuario_perfil'] ?? '') === 'cliente'
            ? 'cliente/agendamentos'
            : 'dashboard';

        redirect($destino);
    }

    /**
     * Encerra a sessão
     */
    public function logout(): void
    {
        session_destroy();
        redirect('login');
    }

    /**
     * Exibe o formulário de "Esqueci minha senha"
     */
    public function forgot(): void
    {
        $msg = $_SESSION['forgot_msg'] ?? '';
        $err = $_SESSION['forgot_err'] ?? '';
        unset($_SESSION['forgot_msg'], $_SESSION['forgot_err']);
        include("Views/auth/forgot.php");
    }

    /**
     * Processa o pedido de envio de link de redefinição
     */
    public function sendReset(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('forgot-password');
        }
        $email = trim($_POST['email'] ?? '');
        if (empty($email)) {
            $_SESSION['forgot_err'] = 'Informe seu e-mail.';
            redirect('forgot-password');
        }

        $usuario = $this->model->buscarPorEmail($email);
        // Mensagem única por segurança, independente de existir ou não
        $_SESSION['forgot_msg'] = 'Se o e-mail existir, enviaremos instruções para redefinir a senha.';

        if ($usuario) {
            try {
                $token   = bin2hex(random_bytes(32));
                $hash    = hash('sha256', $token);
                $expires = date('Y-m-d H:i:s', time() + 3600); // 1h
                $pr = new PasswordResetModel();
                $pr->criar($usuario['usuario_id'], $hash, $expires);
                $link = base_url('reset-password') . '?token=' . $token;
                error_log('[RESET] Link de redefinição: ' . $link . ' (user_id=' . $usuario['usuario_id'] . ')');
            } catch (\Throwable $e) {
                error_log('[RESET] erro ao gerar token: ' . $e->getMessage());
            }
        }

        redirect('forgot-password');
    }

    /**
     * Exibe o formulário de redefinição de senha
     */
    public function resetForm(): void
    {
        $token = $_GET['token'] ?? '';
        $msg = $_SESSION['reset_msg'] ?? '';
        $err = $_SESSION['reset_err'] ?? '';
        unset($_SESSION['reset_msg'], $_SESSION['reset_err']);
        include("Views/auth/reset.php");
    }

    /**
     * Processa a redefinição de senha
     */
    public function reset(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('reset-password');
        }
        $token = $_POST['token'] ?? '';
        $senha = $_POST['senha'] ?? '';
        $conf  = $_POST['confirmar'] ?? '';
        if (empty($token) || empty($senha) || empty($conf)) {
            $_SESSION['reset_err'] = 'Preencha todos os campos.';
            redirect('reset-password?token=' . urlencode($token));
        }
        if ($senha !== $conf) {
            $_SESSION['reset_err'] = 'As senhas não conferem.';
            redirect('reset-password?token=' . urlencode($token));
        }
        if (strlen($senha) < 6) {
            $_SESSION['reset_err'] = 'A senha deve ter pelo menos 6 caracteres.';
            redirect('reset-password?token=' . urlencode($token));
        }

        $hash = hash('sha256', $token);
        $pr = new PasswordResetModel();
        $registro = $pr->buscarValidoPorHash($hash);
        if (!$registro) {
            $_SESSION['reset_err'] = 'Token inválido ou expirado.';
            redirect('reset-password');
        }

        // Atualiza a senha e consome o token
        $ok = $this->model->atualizarSenha((int)$registro['usuario_id'], $senha);
        if ($ok) {
            $pr->consumir((int)$registro['id']);
            $_SESSION['login_erro'] = 'Senha redefinida com sucesso. Faça login.';
            redirect('login');
        } else {
            $_SESSION['reset_err'] = 'Falha ao redefinir a senha.';
            redirect('reset-password?token=' . urlencode($token));
        }
    }

    private function garantirProfissionalDemo(): void
    {
        $emailDemo = 'prof.demo@glowagenda.com';

        try {
            $usuarioExistente = $this->model->buscarPorEmailTodos($emailDemo);
            if ($usuarioExistente) {
                return;
            }

            $hash = password_hash('prof456', PASSWORD_DEFAULT);

            $this->model->insert([
                'usuario_nome'   => 'Profissional Demo',
                'usuario_email'  => $emailDemo,
                'usuario_senha'  => $hash,
                'usuario_perfil' => 'profissional',
                'usuario_ativo'  => 1,
            ]);
        } catch (\Throwable $e) {
            error_log('[AUTH] falha ao garantir profissional demo: ' . $e->getMessage());
        }
    }
}
