<?php

ob_start();
session_start();

// Força o cabeçalho de resposta HTTP para UTF-8 para garantir caracteres especiais como ç e acentos
header('Content-Type: text/html; charset=utf-8');

include_once 'Config/Helpers.php';
include_once 'Config/Security.php';
include_once 'Autoloader.php';

$estabelecimentoInfo = [];
$exibirEstabelecimentoFooter = false;
$papelAtual = currentUserRole();
if ($papelAtual === 'cliente') {
    try {
        $estabelecimentoInfo = (new \Models\EstabelecimentoModel())->obter() ?? [];
    } catch (\Throwable $e) {
        error_log('[APP] erro ao carregar dados do estabelecimento: ' . $e->getMessage());
    }
    $exibirEstabelecimentoFooter = true;
}

// Carrega as rotas
$routes = require __DIR__ . '/Config/Routes.php';

// Pega URI atual
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/') ?: '/';

// Rotas que NÃO precisam de autenticação nem de layout de painel
$rotasPublicas = [
    '/login',
    '/auth/authenticate',
    '/forgot-password',
    '/auth/send-reset',
    '/reset-password',
    '/auth/reset',
    '/cadastro',
    '/cadastro/salvar',
];

// Rotas autenticadas que devem responder sem layout (ex.: JSON)
$rotasSemLayout = [
    '/cliente/agendamentos/resumo',
    '/cliente/agendamentos/disponibilidades',
];

// Se não está logado e a rota não é pública → redireciona para login
if (!isLoggedIn() && !in_array($uri, $rotasPublicas) && $uri !== '/') {
    redirect('login');
}

// Rota raiz: redireciona sempre para login ou dashboard conforme o estado
if ($uri === '/') {
    isLoggedIn() ? redirect('dashboard') : redirect('login');
}

// Se já está logado e tenta acessar login → redireciona para dashboard
if (isLoggedIn() && in_array($uri, ['/login', '/cadastro', '/cadastro/salvar'])) {
    redirect('dashboard');
}

// Função de match de rota com suporte a parâmetros {id}
function matchRoute($uri, $routes)
{
    foreach ($routes as $route => $handler) {
        $pattern = preg_replace('/\{[^\/]+\}/', '([^\/]+)', $route);
        $pattern = "#^" . rtrim($pattern, '/') . "$#";
        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches);
            return [$handler, $matches];
        }
    }
    return [null, []];
}

[$handler, $params] = matchRoute($uri, $routes);

if ($handler) {
    [$controllerName, $method] = $handler;
    $name = "\\Controllers\\" . $controllerName;

    // Rotas públicas: renderiza sem layout de painel
    if (in_array($uri, $rotasPublicas)) {
        $controller = new $name();
        if (method_exists($controller, $method)) {
            call_user_func_array([$controller, $method], $params);
        }
    } elseif (in_array($uri, $rotasSemLayout)) {
        $controller = new $name();
        if (method_exists($controller, $method)) {
            call_user_func_array([$controller, $method], $params);
        }
    } else {
        // Rotas protegidas: envolve com header + sidebar + footer
        include('Views/templates/header.php');
        include('Views/templates/sidebar.php');

        $controller = new $name();
        if (method_exists($controller, $method)) {
            call_user_func_array([$controller, $method], $params);
        } else {
            http_response_code(404);
            echo "<div class='alert alert-danger'>Método '{$method}' não encontrado.</div>";
        }

        include('Views/templates/footer.php');
        include('Views/templates/end.php');
    }
} else {
    // Rota não encontrada
    include('Views/templates/header.php');
    include('Views/templates/sidebar.php');
    echo "<div class='alert alert-warning m-4'>Página não encontrada.</div>";
    include('Views/templates/footer.php');
    include('Views/templates/end.php');
}

ob_end_flush();