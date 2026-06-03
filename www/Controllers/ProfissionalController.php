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
        error_log('[PROF] salvar() start method=' . ($_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN'));
        if (function_exists('requireRole')) requireRole(['admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log('[PROF] salvar() invalid method, redirecting');
            redirect('profissionais');
        }

        $nome  = trim($_POST['usuario_nome'] ?? '');
        $email = trim($_POST['usuario_email'] ?? '');
        $senha = $_POST['usuario_senha'] ?? '';
        $conf  = $_POST['confirmar_senha'] ?? '';
        error_log('[PROF] salvar() received nome_len=' . strlen($nome) . ' email=' . $email . ' senha_len=' . strlen($senha) . ' conf_len=' . strlen($conf));

        $errosValidacao = [];
        if ($nome === '' || $email === '' || $senha === '' || $conf === '') {
            $errosValidacao[] = 'Preencha todos os campos obrigatórios.';
            error_log('[PROF] salvar() validation failed: empty fields');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errosValidacao[] = 'Informe um e-mail válido.';
            error_log('[PROF] salvar() validation failed: invalid email=' . $email);
        }
        if ($senha !== $conf) {
            $errosValidacao[] = 'As senhas não conferem.';
            error_log('[PROF] salvar() validation failed: passwords do not match');
        }
        if (strlen($senha) < 6) {
            $errosValidacao[] = 'A senha deve ter pelo menos 6 caracteres.';
            error_log('[PROF] salvar() validation failed: password too short len=' . strlen($senha));
        }

        if ($this->model->emailExiste($email)) {
            $errosValidacao[] = 'Este e-mail já está cadastrado no sistema.';
            $existente = $this->model->buscarPorEmailTodos($email);
            error_log('[PROF] salvar() validation failed: email already exists email=' . $email . ' existing_id=' . ($existente['usuario_id'] ?? 'null') . ' status=' . ($existente['usuario_ativo'] ?? 'null'));
        }

        if (!empty($errosValidacao)) {
            $_SESSION['msg'] = msg(implode(' ', $errosValidacao), 'danger');
            $_SESSION['form_profissional'] = [
                'usuario_nome' => $nome,
                'usuario_email' => $email,
            ];
            $_SESSION['form_profissional_erro'] = [
                'mensagens' => $errosValidacao,
                'email' => $email,
                'nome' => $nome,
            ];
            redirect('profissionais/novo');
        }

        error_log('[PROF] salvar() all validations passed, preparing data');
        $dados = [
            'usuario_nome'   => $nome,
            'usuario_email'  => $email,
            'usuario_senha'  => $senha, // será hasheada no model
            'usuario_perfil' => 'profissional',
            'usuario_ativo'  => 1,
        ];

        try {
            error_log('[PROF] salvar() calling model->salvar()');
            $this->model->salvar($dados);
            error_log('[PROF] salvar() success, redirecting to profissionais');
            $_SESSION['msg'] = msg('Profissional cadastrado com sucesso!', 'success');
            unset($_SESSION['form_profissional'], $_SESSION['form_profissional_erro']);
            redirect('profissionais');
        } catch (\Throwable $e) {
            error_log('[PROF] salvar() exception: ' . $e->getMessage() . ' code=' . $e->getCode() . ' trace=' . $e->getTraceAsString());
            
            // Identifica o tipo de erro
            if (strpos($e->getMessage(), 'UNIQUE') !== false || strpos($e->getMessage(), 'Duplicate') !== false) {
                error_log('[PROF] salvar() error is UNIQUE constraint violation');
                $_SESSION['msg'] = msg('Este e-mail já está cadastrado. Tente outro.', 'danger');
                $_SESSION['form_profissional_erro'] = [
                    'mensagens' => ['E-mail duplicado no banco de dados.'],
                    'email' => $email,
                    'nome' => $nome,
                    'erro' => $e->getMessage(),
                ];
            } else {
                error_log('[PROF] salvar() error is generic database error');
                $mensagemErro = 'Erro ao cadastrar profissional. Motivo: ' . $e->getMessage();
                $_SESSION['msg'] = msg($mensagemErro, 'danger');
                $_SESSION['form_profissional_erro'] = [
                    'mensagens' => [$mensagemErro],
                    'email' => $email,
                    'nome' => $nome,
                    'erro' => $e->getMessage(),
                    'codigo' => $e->getCode(),
                ];
            }
            $_SESSION['form_profissional'] = [
                'usuario_nome' => $nome,
                'usuario_email' => $email,
            ];
            redirect('profissionais/novo');
        }
    }
}
