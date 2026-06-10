<?php

namespace Controllers;

require_once("Models/Database.php");
require_once("Config/Helpers.php");
use Models\ServicoModel;
use Models\CategoriaModel;

class ServicoController
{
    private ServicoModel   $model;
    private CategoriaModel $categoriaModel;

    public function __construct()
    {
        $this->model          = new ServicoModel();
        $this->categoriaModel = new CategoriaModel();
    }

    public function index(): void
    {
        if (function_exists('requireRole')) requireRole(['admin','profissional']);
        $busca      = trim($_GET['busca'] ?? '');
        $categoria  = $_GET['categoria'] ?? '';
        $ativo      = $_GET['ativo'] ?? '';
        $msg        = $_SESSION['msg'] ?? '';
        unset($_SESSION['msg']);

        view('servicos/index', [
            'pagina'     => 'Serviços',
            'servicos'   => $this->model->listar($busca, $categoria, $ativo),
            'categorias' => $this->categoriaModel->listar(),
            'busca'      => $busca,
            'categoria'  => $categoria,
            'ativo'      => $ativo,
            'msg'        => $msg,
        ]);
    }

    public function novo(): void
    {
        if (function_exists('requireRole')) requireRole(['admin','profissional']);
        $msg = $_SESSION['msg'] ?? '';
        unset($_SESSION['msg']);
        view('servicos/form', [
            'pagina'     => 'Novo Serviço',
            'servico'    => [],
            'categorias' => $this->categoriaModel->listar(),
            'msg'        => $msg,
        ]);
    }

    public function salvar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('servicos');
        }
        if (function_exists('requireRole')) requireRole(['admin','profissional']);

        $dados = [
            'servico_nome'      => trim($_POST['servico_nome'] ?? ''),
            'servico_descricao' => trim($_POST['servico_descricao'] ?? ''),
            'servico_preco'     => (float) str_replace(',', '.', $_POST['servico_preco'] ?? 0),
            'servico_duracao'   => (int) ($_POST['servico_duracao'] ?? 0),
            'categoria_id'      => (int) ($_POST['categoria_id'] ?? 0),
            'servico_ativo'     => 1,
        ];

        $novaCategoria = trim($_POST['nova_categoria'] ?? '');
        if (!empty($novaCategoria)) {
            $categoria = $this->categoriaModel->buscarPorNome($novaCategoria);
            if (!$categoria) {
                $categoriaId = $this->categoriaModel->criar($novaCategoria);
            } else {
                $categoriaId = (int)($categoria['categoria_id'] ?? 0);
            }
            $dados['categoria_id'] = $categoriaId;
        }

        if (empty($dados['servico_nome']) || $dados['categoria_id'] === 0) {
            $_SESSION['msg'] = msg('Nome e categoria são obrigatórios.', 'danger');
            redirect('servicos/novo');
        }

        $this->model->salvar($dados);
        $_SESSION['msg'] = msg('Serviço cadastrado com sucesso!', 'success');
        redirect('servicos');
    }

    public function editar(int $id): void
    {
        if (function_exists('requireRole')) requireRole(['admin','profissional']);
        $servico = $this->model->buscarPorId($id);
        if (!$servico) redirect('servicos');
        $msg = $_SESSION['msg'] ?? '';
        unset($_SESSION['msg']);
        view('servicos/form', [
            'pagina'     => 'Editar Serviço',
            'servico'    => $servico,
            'categorias' => $this->categoriaModel->listar(),
            'msg'        => $msg,
        ]);
    }

    public function atualizar(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('servicos');
        }
        if (function_exists('requireRole')) requireRole(['admin','profissional']);

        $dados = [
            'servico_nome'      => trim($_POST['servico_nome'] ?? ''),
            'servico_descricao' => trim($_POST['servico_descricao'] ?? ''),
            'servico_preco'     => (float) str_replace(',', '.', $_POST['servico_preco'] ?? 0),
            'servico_duracao'   => (int) ($_POST['servico_duracao'] ?? 0),
            'categoria_id'      => (int) ($_POST['categoria_id'] ?? 0),
            'servico_ativo'     => isset($_POST['servico_ativo']) ? 1 : 0,
        ];

        $novaCategoria = trim($_POST['nova_categoria'] ?? '');
        if (!empty($novaCategoria)) {
            $categoria = $this->categoriaModel->buscarPorNome($novaCategoria);
            if (!$categoria) {
                $categoriaId = $this->categoriaModel->criar($novaCategoria);
            } else {
                $categoriaId = (int)($categoria['categoria_id'] ?? 0);
            }
            $dados['categoria_id'] = $categoriaId;
        }

        if (empty($dados['servico_nome']) || $dados['categoria_id'] === 0) {
            $_SESSION['msg'] = msg('Nome e categoria são obrigatórios.', 'danger');
            redirect("servicos/editar/{$id}");
        }

        $this->model->atualizar($id, $dados);
        $_SESSION['msg'] = msg('Serviço atualizado com sucesso!', 'success');
        redirect('servicos');
    }

    public function criarCategoria(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('servicos');
        }
        if (function_exists('requireRole')) requireRole(['admin','profissional']);

        $nome     = trim($_POST['categoria_nome'] ?? '');
        $segmento = trim($_POST['categoria_segmento'] ?? '');
        if ($nome === '') {
            $_SESSION['msg'] = msg('Informe um nome válido para a categoria.', 'danger');
            redirect('servicos');
        }

        if ($segmento !== '') {
            $nome = $segmento . ' • ' . $nome;
        }

        $categoriaExistente = $this->categoriaModel->buscarPorNome($nome);
        if ($categoriaExistente) {
            $_SESSION['msg'] = msg('Essa categoria já está disponível.', 'info');
            redirect('servicos');
        }

        $this->categoriaModel->criar($nome);
        $_SESSION['msg'] = msg('Categoria criada com sucesso!', 'success');
        redirect('servicos');
    }

    public function excluir(int $id): void
    {
        if (function_exists('requireRole')) requireRole(['admin','profissional']);
        $this->model->remover($id);
        $_SESSION['msg'] = msg('Serviço removido.', 'warning');
        redirect('servicos');
    }
}
