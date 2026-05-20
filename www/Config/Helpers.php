<?php

function isLoggedIn(): bool {
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

function redirect(string $path): void {
    header('Location: ' . base_url($path));
    exit;
}

function view($viewName, $data = [])
{
    $viewPath = "Views/{$viewName}.php";
    if (file_exists($viewPath)) {
        extract($data);
        include $viewPath;
    } else {
        echo "<div class='alert alert-danger'>View '{$viewName}' não encontrada.</div>";
    }
}

function base_url($path = '') {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $scriptName = dirname($_SERVER['SCRIPT_NAME']);
    $url = rtrim($protocol . "://" . $host . $scriptName, '/');
    return $url . '/' . ltrim($path, '/');
}

function msg($texto, $tipo = 'success'){
    $alertType = "alert-{$tipo}";
    $icone = ($tipo === 'danger')
        ? '<i class="bi bi-exclamation-triangle-fill"></i>'
        : '<i class="bi bi-check-circle-fill"></i>';
    return '<div class="alert ' . $alertType . ' alert-dismissible fade show" role="alert">'
         . $icone . ' ' . $texto
         . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>'
         . '</div>';
}

function formatarDinheiro(float $valor): string {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function formatarData(string $data): string {
    return date('d/m/Y', strtotime($data));
}

function formatarDuracao(int $minutos): string {
    if ($minutos < 60) return "{$minutos} min";
    $h = intdiv($minutos, 60);
    $m = $minutos % 60;
    return $m > 0 ? "{$h}h {$m}min" : "{$h}h";
}
?>