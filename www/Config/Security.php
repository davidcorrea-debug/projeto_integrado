<?php

function currentUserId(): ?int {
    return isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : null;
}

function currentUserRole(): string {
    return $_SESSION['usuario_perfil'] ?? '';
}

function hasRole($roles): bool {
    $role = currentUserRole();
    $roles = is_array($roles) ? $roles : [$roles];
    return in_array($role, $roles, true);
}

function requireRole($roles): void {
    if (!hasRole($roles)) {
        http_response_code(403);
        echo 'Acesso negado';
        exit;
    }
}
