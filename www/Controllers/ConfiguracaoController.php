<?php

namespace Controllers;

require_once("Models/Database.php");
require_once("Config/Helpers.php");
require_once("Config/Security.php");

use Models\Database;
use Models\UsuarioModel;
use Models\ClienteModel;

class ConfiguracaoController
{
    private Database $database;
    private UsuarioModel $usuarioModel;
    private ClienteModel $clienteModel;

    public function __construct()
    {
        if (!function_exists('isLoggedIn') || !isLoggedIn()) {
            redirect('login');
        }

        $this->database     = new Database();
        $connection         = $this->database->getConnection();
        $this->usuarioModel = new UsuarioModel(connection: $connection);
        $this->clienteModel = new ClienteModel(connection: $connection);
    }

    public function editar(): void
    {
        $usuarioId = currentUserId();
        if (!$usuarioId) {
            redirect('login');
        }

        $usuario = $this->usuarioModel->buscarComSenhaPorId($usuarioId);
        if (!$usuario) {
            session_destroy();
            redirect('login');
        }

        $perfil  = $usuario['usuario_perfil'] ?? '';
        $cliente = null;
        $telefoneCliente = '';
        if ($perfil === 'cliente') {
            $cliente = $this->clienteModel->buscarPorUsuarioId($usuarioId, $usuario['usuario_email'] ?? null);
            if (is_array($cliente)) {
                $telefoneCliente = $cliente['cliente_telefone'] ?? '';
            }
        }

        $msg    = $_SESSION['msg'] ?? '';
        $erros  = $_SESSION['config_erros'] ?? [];
        $dados  = $_SESSION['config_dados'] ?? [];
        unset($_SESSION['msg'], $_SESSION['config_erros'], $_SESSION['config_dados']);

        if (empty($dados)) {
            $dados = [
                'usuario_nome'     => $usuario['usuario_nome'] ?? '',
                'usuario_email'    => $usuario['usuario_email'] ?? '',
                'cliente_telefone' => $telefoneCliente,
            ];
        } else {
            if (!array_key_exists('cliente_telefone', $dados)) {
                $dados['cliente_telefone'] = $telefoneCliente;
            }
        }

        view('configuracoes/form', [
            'pagina'  => 'Configurações da Conta',
            'usuario' => $usuario,
            'cliente' => $cliente,
            'perfil'  => $perfil,
            'msg'     => $msg,
            'erros'   => $erros,
            'dados'   => $dados,
        ]);
    }

    public function atualizar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('configuracoes');
        }

        $usuarioId = currentUserId();
        if (!$usuarioId) {
            redirect('login');
        }

        $usuarioAtual = $this->usuarioModel->buscarComSenhaPorId($usuarioId);
        if (!$usuarioAtual) {
            session_destroy();
            redirect('login');
        }

        $perfil = $usuarioAtual['usuario_perfil'] ?? '';

        $dadosFormulario = [
            'usuario_nome'      => trim($_POST['usuario_nome'] ?? ''),
            'usuario_email'     => strtolower(trim($_POST['usuario_email'] ?? '')),
            'cliente_telefone'  => trim($_POST['cliente_telefone'] ?? ''),
        ];

        $senhaAtual     = $_POST['senha_atual'] ?? '';
        $novaSenha      = $_POST['nova_senha'] ?? '';
        $confirmarSenha = $_POST['confirmar_senha'] ?? '';

        $erros = [];

        if ($dadosFormulario['usuario_nome'] === '') {
            $erros[] = 'Informe seu nome completo.';
        }

        if ($dadosFormulario['usuario_email'] === '' || !filter_var($dadosFormulario['usuario_email'], FILTER_VALIDATE_EMAIL)) {
            $erros[] = 'Informe um e-mail válido.';
        }

        $registroEmail = $this->usuarioModel->buscarPorEmailTodos($dadosFormulario['usuario_email']);
        if ($registroEmail && (int)$registroEmail['usuario_id'] !== (int)$usuarioId) {
            $erros[] = 'Este e-mail já está em uso por outro usuário.';
        }

        $alterarSenha = $novaSenha !== '' || $confirmarSenha !== '' || $senhaAtual !== '';
        if ($alterarSenha) {
            if ($senhaAtual === '' || $novaSenha === '' || $confirmarSenha === '') {
                $erros[] = 'Para alterar a senha informe a senha atual, nova senha e confirmação.';
            } else {
                if (!password_verify($senhaAtual, $usuarioAtual['usuario_senha'] ?? '')) {
                    $erros[] = 'Senha atual incorreta.';
                }
                if (strlen($novaSenha) < 6) {
                    $erros[] = 'A nova senha deve possuir pelo menos 6 caracteres.';
                }
                if ($novaSenha !== $confirmarSenha) {
                    $erros[] = 'A confirmação de senha não confere.';
                }
            }
        }

        if (!empty($erros)) {
            $_SESSION['config_erros'] = $erros;
            $_SESSION['config_dados'] = $dadosFormulario;
            redirect('configuracoes');
        }

        $dadosUsuario = [
            'usuario_nome'  => $dadosFormulario['usuario_nome'],
            'usuario_email' => $dadosFormulario['usuario_email'],
        ];

        if ($alterarSenha && empty($erros)) {
            $dadosUsuario['usuario_senha'] = $novaSenha;
        }

        try {
            $this->database->transaction(function () use ($perfil, $usuarioId, $dadosUsuario, $dadosFormulario, $usuarioAtual) {
                $this->usuarioModel->atualizar($usuarioId, $dadosUsuario);

                if ($perfil === 'cliente') {
                    $cliente = $this->clienteModel->buscarPorUsuarioId($usuarioId, $usuarioAtual['usuario_email'] ?? null);

                    $payloadCliente = [
                        'cliente_nome'       => $dadosUsuario['usuario_nome'],
                        'cliente_email'      => $dadosUsuario['usuario_email'],
                        'cliente_telefone'   => $dadosFormulario['cliente_telefone'],
                        'cliente_usuario_id' => $usuarioId,
                    ];

                    if ($cliente) {
                        $this->clienteModel->atualizar((int)$cliente['cliente_id'], $payloadCliente);
                    } else {
                        $this->clienteModel->salvar($payloadCliente);
                    }
                }
            });
        } catch (\Throwable $e) {
            error_log('[CONFIG] erro ao atualizar conta: ' . $e->getMessage());
            $_SESSION['config_erros'] = ['Erro ao atualizar configurações. Tente novamente mais tarde.'];
            $_SESSION['config_dados'] = $dadosFormulario;
            redirect('configuracoes');
        }

        $_SESSION['usuario_nome']  = $dadosUsuario['usuario_nome'];
        $_SESSION['usuario_email'] = $dadosUsuario['usuario_email'];
        $_SESSION['msg'] = msg('Dados atualizados com sucesso!', 'success');
        redirect('configuracoes');
    }
}
