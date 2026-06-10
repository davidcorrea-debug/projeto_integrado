<?php

namespace Controllers;

require_once 'Models/Database.php';
require_once 'Models/EstabelecimentoModel.php';
require_once 'Config/Helpers.php';
require_once 'Config/Security.php';

use Models\Database;
use Models\EstabelecimentoModel;

class EstabelecimentoController
{
    private Database $database;
    private EstabelecimentoModel $model;

    public function __construct()
    {
        if (!function_exists('isLoggedIn') || !isLoggedIn()) {
            redirect('login');
        }

        $this->database = new Database();
        $this->model = new EstabelecimentoModel($this->database->getConnection());
    }

    public function editar(): void
    {
        requireRole(['admin', 'profissional']);

        $dados = $this->model->obter();

        view('configuracoes/estabelecimento', [
            'pagina'           => 'Informações do Estabelecimento',
            'estabelecimento'  => $dados,
            'msg'              => $_SESSION['msg'] ?? '',
            'erros'            => $_SESSION['estabelecimento_erros'] ?? [],
            'dados'            => $_SESSION['estabelecimento_dados'] ?? []
        ]);

        unset($_SESSION['msg'], $_SESSION['estabelecimento_erros'], $_SESSION['estabelecimento_dados']);
    }

    public function salvar(): void
    {
        requireRole(['admin', 'profissional']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('configuracoes');
        }

        $payload = [
            'nome'            => $_POST['nome'] ?? '',
            'nome_fantasia'   => $_POST['nome_fantasia'] ?? '',
            'cnpj'            => $_POST['cnpj'] ?? '',
            'telefone'        => $_POST['telefone'] ?? '',
            'email'           => $_POST['email'] ?? '',
            'endereco'        => $_POST['endereco'] ?? '',
            'cep'             => $_POST['cep'] ?? '',
            'localizacao_url' => $_POST['localizacao_url'] ?? '',
            'instagram'       => $_POST['instagram'] ?? '',
            'facebook'        => $_POST['facebook'] ?? '',
            'site'            => $_POST['site'] ?? '',
        ];

        $erros = $this->validar($payload);

        if (!empty($erros)) {
            $_SESSION['estabelecimento_erros'] = $erros;
            $_SESSION['estabelecimento_dados'] = $payload;
            redirect('estabelecimento');
        }

        $this->model->salvar($payload);
        $_SESSION['msg'] = msg('Informações do estabelecimento atualizadas com sucesso!', 'success');

        redirect('estabelecimento');
    }

    private function validar(array &$payload): array
    {
        $erros = [];

        if ($payload['nome'] === '') {
            $erros[] = 'Informe o nome do salão.';
        }

        if ($payload['telefone'] === '') {
            $erros[] = 'Informe um telefone de contato.';
        }

        if ($payload['endereco'] === '') {
            $erros[] = 'Informe o endereço completo.';
        }

        if ($payload['cep'] === '') {
            $erros[] = 'Informe o CEP do estabelecimento.';
        }

        if ($payload['cnpj'] === '') {
            $erros[] = 'Informe o CNPJ.';
        }

        if ($payload['email'] !== '' && !filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
            $erros[] = 'Informe um e-mail válido.';
        }

        $payload['cnpj'] = $this->normalizarCnpj($payload['cnpj']);

        return $erros;
    }

    private function normalizarCnpj(string $cnpj): string
    {
        $somenteNumeros = preg_replace('/\D+/', '', $cnpj);
        if (strlen($somenteNumeros) === 14) {
            return substr($somenteNumeros, 0, 2) . '.' .
                   substr($somenteNumeros, 2, 3) . '.' .
                   substr($somenteNumeros, 5, 3) . '/' .
                   substr($somenteNumeros, 8, 4) . '-' .
                   substr($somenteNumeros, 12, 2);
        }

        return $cnpj;
    }
}
