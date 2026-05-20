<?php

namespace Models;

use Models\Database;

class ClienteModel extends Database
{
    public function __construct()
    {
        parent::__construct('clientes');
    }

    /**
     * Lista todos os clientes ordenados por nome
     */
    public function listar(string $busca = ''): array
    {
        if (!empty($busca)) {
            $stmt = $this->execute(
                "SELECT * FROM clientes
                 WHERE cliente_nome LIKE ? OR cliente_telefone LIKE ? OR cliente_email LIKE ?
                 ORDER BY cliente_nome ASC",
                ["%{$busca}%", "%{$busca}%", "%{$busca}%"]
            );
        } else {
            $stmt = $this->execute(
                "SELECT * FROM clientes ORDER BY cliente_nome ASC"
            );
        }
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Busca cliente por ID
     */
    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->execute(
            "SELECT * FROM clientes WHERE cliente_id = ? LIMIT 1",
            [$id]
        );
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Salva novo cliente
     */
    public function salvar(array $dados): int
    {
        return $this->insert($dados);
    }

    /**
     * Atualiza dados do cliente
     */
    public function atualizar(int $id, array $dados): bool
    {
        return $this->update("cliente_id = {$id}", $dados);
    }

    /**
     * Remove cliente
     */
    public function remover(int $id): bool
    {
        return $this->delete("cliente_id = {$id}");
    }

    /**
     * Total de clientes cadastrados
     */
    public function total(): int
    {
        $stmt = $this->execute("SELECT COUNT(*) as total FROM clientes");
        return (int) $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
    }
}
