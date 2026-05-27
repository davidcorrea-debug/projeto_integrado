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
                "SELECT id, nome, email, telefone, cpf_cnpj, ativo FROM usuario
                 WHERE tipo_usuario = 'CLIENTE' AND ativo = 1
                 AND (nome LIKE ? OR telefone LIKE ? OR email LIKE ?)
                 ORDER BY nome ASC",
                ["%{$busca}%", "%{$busca}%", "%{$busca}%"]
            );
        } else {
            $stmt = $this->execute(
                "SELECT id, nome, email, telefone, cpf_cnpj, ativo FROM usuario 
                 WHERE tipo_usuario = 'CLIENTE' AND ativo = 1 ORDER BY nome ASC"
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
            "SELECT id, nome, email, telefone, cpf_cnpj, data_nascimento, ativo FROM usuario 
             WHERE id = ? AND tipo_usuario = 'CLIENTE' LIMIT 1",
            [$id]
        );
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Salva novo cliente (Insere como tipo_usuario = 'CLIENTE')
     */
    public function salvar(array $dados): int
    {
        $dados['tipo_usuario'] = 'CLIENTE';
        $dados['ativo'] = true;
        return $this->insert($dados);
    }

    /**
     * Atualiza dados do cliente
     */
    public function atualizar(int $id, array $dados): bool
    {
        // Garante que continua como CLIENTE
        $dados['tipo_usuario'] = 'CLIENTE';
        return $this->update("id = {$id} AND tipo_usuario = 'CLIENTE'", $dados);
    }

    /**
     * Remove cliente
     */
    public function remover(int $id): bool
    {
        return $this->update(
            "id = {$id} AND tipo_usuario = 'CLIENTE'",
            ['ativo' => 0, 'deletado_em' => date('Y-m-d H:i:s')]
        );
    }

    /**
     * Total de clientes cadastrados
     */
    public function total(): int
    {
        $stmt = $this->execute("SELECT COUNT(*) as total FROM usuario WHERE tipo_usuario = 'CLIENTE' AND ativo = 1");
        return (int) $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
    }

    /**
     * Busca cliente por e-mail
     */
    public function buscarPorEmail(string $email): ?array
    {
        $stmt = $this->execute(
            "SELECT id, nome, email, telefone, cpf_cnpj FROM usuario 
             WHERE email = ? AND tipo_usuario = 'CLIENTE' AND ativo = 1 LIMIT 1",
            [$email]
        );
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}
