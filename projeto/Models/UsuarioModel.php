<?php

namespace Models;

use Models\Database;

class UsuarioModel extends Database
{
    public function __construct()
    {
        parent::__construct('usuarios');
    }

    /**
     * Busca um usuário pelo e-mail (para login)
     */
    public function buscarPorEmail(string $email): ?array
    {
        $stmt = $this->execute(
            "SELECT * FROM usuarios WHERE usuario_email = ? AND usuario_ativo = 1 LIMIT 1",
            [$email]
        );
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Retorna todos os usuários ativos
     */
    public function listar(): array
    {
        $stmt = $this->execute(
            "SELECT usuario_id, usuario_nome, usuario_email, usuario_perfil, usuario_ativo, criado_em
             FROM usuarios ORDER BY usuario_nome ASC"
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Busca usuário por ID
     */
    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->execute(
            "SELECT usuario_id, usuario_nome, usuario_email, usuario_perfil, usuario_ativo
             FROM usuarios WHERE usuario_id = ? LIMIT 1",
            [$id]
        );
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Salva novo usuário
     */
    public function salvar(array $dados): int
    {
        $dados['usuario_senha'] = password_hash($dados['usuario_senha'], PASSWORD_DEFAULT);
        return $this->insert($dados);
    }

    /**
     * Atualiza dados do usuário
     */
    public function atualizar(int $id, array $dados): bool
    {
        if (!empty($dados['usuario_senha'])) {
            $dados['usuario_senha'] = password_hash($dados['usuario_senha'], PASSWORD_DEFAULT);
        } else {
            unset($dados['usuario_senha']);
        }
        return $this->update("usuario_id = {$id}", $dados);
    }
}
