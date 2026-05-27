<?php

namespace Models;

use Models\Database;

class ProfissionalModel extends Database
{
    public function __construct()
    {
        parent::__construct('usuario');
    }

    /**
     * Lista todos os profissionais ativos
     */
    public function listar(string $busca = ''): array
    {
        if (!empty($busca)) {
            $stmt = $this->execute(
                "SELECT id, nome, email, telefone, ativo FROM usuario
                 WHERE tipo_usuario = 'PROFISSIONAL' AND ativo = 1
                 AND (nome LIKE ? OR email LIKE ?)
                 ORDER BY nome ASC",
                ["%{$busca}%", "%{$busca}%"]
            );
        } else {
            $stmt = $this->execute(
                "SELECT id, nome, email, telefone, ativo FROM usuario 
                 WHERE tipo_usuario = 'PROFISSIONAL' AND ativo = 1 ORDER BY nome ASC"
            );
        }
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Busca profissional por ID
     */
    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->execute(
            "SELECT id, nome, email, telefone, cpf_cnpj, data_nascimento, ativo FROM usuario 
             WHERE id = ? AND tipo_usuario = 'PROFISSIONAL' LIMIT 1",
            [$id]
        );
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}