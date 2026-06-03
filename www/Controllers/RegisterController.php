<?php

namespace Controllers;

require_once("Models/Database.php");
require_once("Config/Helpers.php");

use Models\ClienteModel;
use Models\UsuarioModel;
use Models\Database;

class RegisterController
{
    private UsuarioModel $usuarioModel;
    private ClienteModel $clienteModel;
    private Database $database;

    public function __construct()
    {
        $this->database     = new Database();
        $connection         = $this->database->getConnection();
        $this->usuarioModel = new UsuarioModel(connection: $connection);
        $this->clienteModel = new ClienteModel(connection: $connection);
    }

    public function form(): void
    {
        if (isLoggedIn()) {
            redirect('dashboard');
        }

        $erros = $_SESSION['cadastro_erros'] ?? [];
        $dados = $_SESSION['cadastro_dados'] ?? [];
        $debug = $_SESSION['cadastro_debug'] ?? [];
        unset($_SESSION['cadastro_erros'], $_SESSION['cadastro_dados'], $_SESSION['cadastro_debug']);

        include('Views/auth/register.php');
    }

    public function salvar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('cadastro');
        }

        $dados = [
            'nome'      => trim($_POST['nome'] ?? ''),
            'email'     => strtolower(trim($_POST['email'] ?? '')),
            'telefone'  => trim($_POST['telefone'] ?? ''),
            'senha'     => $_POST['senha'] ?? '',
            'confirmar' => $_POST['confirmar'] ?? '',
        ];

        error_log('[REGISTER] payload=' . json_encode([
            'nome'     => $dados['nome'],
            'email'    => $dados['email'],
            'telefone' => $dados['telefone']
        ], JSON_UNESCAPED_UNICODE));

        $debug = [];

        $debug[] = 'payload=' . json_encode([
            'nome'     => $dados['nome'],
            'email'    => $dados['email'],
            'telefone' => $dados['telefone']
        ], JSON_UNESCAPED_UNICODE);

        $erros = $this->validar($dados, $debug);
        if (!empty($erros)) {
            error_log('[REGISTER] validation_errors=' . json_encode($erros, JSON_UNESCAPED_UNICODE));
            $_SESSION['cadastro_erros'] = $erros;
            $_SESSION['cadastro_dados'] = $dados;
            $_SESSION['cadastro_debug'] = $debug;
            redirect('cadastro');
        }

        try {
            $this->database->transaction(function () use (&$debug, $dados) {
                error_log('[REGISTER] iniciando transacao cadastro email=' . $dados['email']);
                $debug[] = 'transacao_iniciada';
                $usuarioId = $this->usuarioModel->salvar([
                    'usuario_nome'   => $dados['nome'],
                    'usuario_email'  => $dados['email'],
                    'usuario_senha'  => $dados['senha'],
                    'usuario_perfil' => 'cliente',
                    'usuario_ativo'  => 1,
                ]);
                $debug[] = 'usuario_criado_id=' . $usuarioId;

                $clienteExistente = $this->clienteModel->buscarPorEmail($dados['email']);
                error_log('[REGISTER] cliente_existente=' . ($clienteExistente ? '1' : '0'));
                $debug[] = 'cliente_existente=' . ($clienteExistente ? '1' : '0');
                if ($clienteExistente) {
                    $this->clienteModel->atualizar((int)$clienteExistente['cliente_id'], [
                        'cliente_nome'        => $dados['nome'],
                        'cliente_email'       => $dados['email'],
                        'cliente_telefone'    => $dados['telefone'],
                        'cliente_usuario_id'  => (int)$usuarioId,
                    ]);
                    $debug[] = 'cliente_atualizado_id=' . (int)$clienteExistente['cliente_id'];
                } else {
                    $this->clienteModel->salvar([
                        'cliente_nome'       => $dados['nome'],
                        'cliente_email'      => $dados['email'],
                        'cliente_telefone'   => $dados['telefone'],
                        'cliente_usuario_id' => (int)$usuarioId,
                    ]);
                    $debug[] = 'cliente_criado_novo';
                }
            });
            $debug[] = 'transacao_concluida';
            $_SESSION['cadastro_debug'] = $debug;
        } catch (\Throwable $e) {
            error_log('[REGISTER] erro ao criar usuário: ' . $e->getMessage());
            error_log('[REGISTER] trace: ' . $e->getTraceAsString());
            $_SESSION['cadastro_erros'] = ['Ocorreu um erro ao processar seu cadastro. Tente novamente.'];
            $_SESSION['cadastro_dados'] = $dados;
            $debug[] = 'exception=' . $e->getMessage();
            $_SESSION['cadastro_debug'] = $debug;
            redirect('cadastro');
        }

        $_SESSION['login_sucesso'] = 'Cadastro realizado com sucesso! Faça login com seu e-mail e senha.';
        unset($_SESSION['cadastro_debug']);
        redirect('login');
    }

    private function validar(array $dados, array &$debug): array
    {
        $erros = [];

        if (empty($dados['nome'])) {
            $erros[] = 'Informe seu nome completo.';
        }

        if (empty($dados['email']) || !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $erros[] = 'Informe um e-mail válido.';
        }

        if (empty($dados['senha']) || strlen($dados['senha']) < 6) {
            $erros[] = 'Crie uma senha com pelo menos 6 caracteres.';
        }

        if ($dados['senha'] !== $dados['confirmar']) {
            $erros[] = 'As senhas digitadas não conferem.';
        }

        if (!empty($dados['email'])) {
            $usuario = $this->usuarioModel->buscarPorEmailTodos($dados['email']);
            $debug[] = 'validar_buscar_usuario_found=' . ($usuario ? '1' : '0') . ($usuario ? (' ativo=' . ($usuario['usuario_ativo'] ?? 'null') . ' id=' . ($usuario['usuario_id'] ?? 'null')) : '');
            error_log('[REGISTER] validar email=' . $dados['email'] . ' encontrou=' . ($usuario ? '1' : '0') . ($usuario ? (' ativo=' . ($usuario['usuario_ativo'] ?? 'null') . ' id=' . ($usuario['usuario_id'] ?? 'null')) : ''));
            if ($usuario && (int)($usuario['usuario_ativo'] ?? 0) === 1) {
                $erros[] = 'Já existe um usuário ativo com este e-mail.';
            }
        }

        return $erros;
    }
}
