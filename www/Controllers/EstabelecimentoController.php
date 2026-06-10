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

        $registroAtual = $this->model->obter() ?? [];

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

        $upload = $this->processarLogoUpload($registroAtual['logo'] ?? null);
        if (!empty($upload['erros'])) {
            $erros = array_merge($erros, $upload['erros']);
        }

        if (!empty($erros)) {
            $_SESSION['estabelecimento_erros'] = $erros;
            $_SESSION['estabelecimento_dados'] = $payload;
            redirect('estabelecimento');
        }

        $arquivoParaRemover = null;

        if ($upload['remover']) {
            $payload['logo'] = null;
            $arquivoParaRemover = $upload['logo_atual'] ?? null;
        } elseif (!empty($upload['novo'])) {
            if (!$this->moverArquivoLogo($upload['tmp'], $upload['destino'])) {
                $_SESSION['estabelecimento_erros'] = ['Não foi possível salvar a foto do estabelecimento. Tente novamente.'];
                $_SESSION['estabelecimento_dados'] = $payload;
                redirect('estabelecimento');
            }

            $payload['logo'] = $upload['novo'];
            $arquivoParaRemover = $upload['logo_atual'] ?? null;
        }

        $this->model->salvar($payload);

        if ($arquivoParaRemover) {
            $this->removerArquivoLogo($arquivoParaRemover);
        }
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

    private function processarLogoUpload(?string $logoAtual): array
    {
        $resultado = [
            'erros'      => [],
            'novo'       => null,
            'tmp'        => null,
            'destino'    => null,
            'remover'    => false,
            'logo_atual' => $logoAtual,
        ];

        if (!empty($_POST['remover_logo'])) {
            $resultado['remover'] = true;
            return $resultado;
        }

        if (empty($_FILES['logo']) || !is_array($_FILES['logo'])) {
            return $resultado;
        }

        $arquivo = $_FILES['logo'];
        if (($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $resultado;
        }

        if (($arquivo['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            $resultado['erros'][] = 'Falha ao enviar a foto do estabelecimento. Tente novamente.';
            return $resultado;
        }

        if (($arquivo['size'] ?? 0) > (2 * 1024 * 1024)) {
            $resultado['erros'][] = 'A foto do estabelecimento deve ter no máximo 2MB.';
            return $resultado;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($arquivo['tmp_name']) ?: '';
        $finfo = null;

        $extensoesPermitidas = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];

        if (!array_key_exists($mime, $extensoesPermitidas)) {
            $resultado['erros'][] = 'Carregue uma imagem nos formatos JPG, PNG ou WEBP.';
            return $resultado;
        }

        try {
            $nomeArquivo = 'logo_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $extensoesPermitidas[$mime];
        } catch (\Throwable $e) {
            $resultado['erros'][] = 'Não foi possível gerar o nome do arquivo enviado. Tente novamente.';
            return $resultado;
        }

        $destinoDir = dirname(__DIR__) . '/public/uploads/estabelecimento';
        $destinoPath = $destinoDir . '/' . $nomeArquivo;

        $resultado['novo']    = 'public/uploads/estabelecimento/' . $nomeArquivo;
        $resultado['tmp']     = $arquivo['tmp_name'];
        $resultado['destino'] = $destinoPath;

        return $resultado;
    }

    private function moverArquivoLogo(string $tmp, string $destino): bool
    {
        $diretorio = dirname($destino);
        if (!is_dir($diretorio) && !mkdir($diretorio, 0755, true) && !is_dir($diretorio)) {
            return false;
        }

        return move_uploaded_file($tmp, $destino);
    }

    private function removerArquivoLogo(?string $caminhoRelativo): void
    {
        if (empty($caminhoRelativo)) {
            return;
        }

        $caminhoRelativo = ltrim($caminhoRelativo, '/');
        $basePermitida   = 'public/uploads/estabelecimento/';

        if (strpos($caminhoRelativo, $basePermitida) !== 0) {
            return;
        }

        $arquivoAbsoluto = dirname(__DIR__) . '/' . $caminhoRelativo;
        if (is_file($arquivoAbsoluto)) {
            @unlink($arquivoAbsoluto);
        }
    }
}
