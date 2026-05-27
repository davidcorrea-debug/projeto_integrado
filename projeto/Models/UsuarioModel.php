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
            "SELECT id, nome, email, tipo_usuario, senha_hash, ativo FROM usuario WHERE email = ? AND ativo = 1 LIMIT 1",
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
            "SELECT id, nome, email, tipo_usuario, ativo, data_criacao FROM usuario WHERE ativo = 1 ORDER BY nome ASC"
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Busca usuário por ID
     */
    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->execute(
            "SELECT id, nome, email, telefone, cpf_cnpj, tipo_usuario, ativo FROM usuario WHERE id = ? LIMIT 1",
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
        if (!empty($dados['senha_hash'])) {
            $dados['senha_hash'] = password_hash($dados['senha_hash'], PASSWORD_DEFAULT);
        } else {
            unset($dados['senha_hash']);
        }
        return $this->update("id = {$id}", $dados);
    }
}
