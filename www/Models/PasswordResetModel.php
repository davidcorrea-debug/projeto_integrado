<?php

namespace Models;

use Models\Database;

class PasswordResetModel extends Database
{
    public function __construct()
    {
        parent::__construct('password_resets');
    }

    public function criar(int $usuarioId, string $tokenHash, string $expiresAt): int
    {
        $dados = [
            'usuario_id' => $usuarioId,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt,
        ];
        return $this->insert($dados);
    }

    public function buscarValidoPorHash(string $hash): ?array
    {
        $stmt = $this->execute(
            "SELECT * FROM password_resets WHERE token_hash = ? AND expires_at > NOW() LIMIT 1",
            [$hash]
        );
        $r = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $r ?: null;
    }

    public function consumir(int $id): bool
    {
        return $this->delete('id = ' . (int)$id);
    }
}
