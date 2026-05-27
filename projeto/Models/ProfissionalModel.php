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
    /**
     * Lista profissionais de um estabelecimento
     */
    public function listarPorEstabelecimento(int $estabelecimento_id): array
    {
        $stmt = $this->execute(
            "SELECT u.id, u.nome, u.email, u.telefone, pe.data_inicio, pe.ativo
             FROM usuario u
             INNER JOIN profissional_estabelecimento pe ON u.id = pe.profissional_id
             WHERE pe.estabelecimento_id = ? AND pe.ativo = 1
             ORDER BY u.nome ASC",
            [$estabelecimento_id]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Busca profissional por email
     */
    public function buscarPorEmail(string $email): ?array
    {
        $stmt = $this->execute(
            "SELECT id, nome, email, telefone, cpf_cnpj FROM usuario 
             WHERE email = ? AND tipo_usuario = 'PROFISSIONAL' AND ativo = 1 LIMIT 1",
            [$email]
        );
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Salva novo profissional
     */
    public function salvar(array $dados): int
    {
        $dados['tipo_usuario'] = 'PROFISSIONAL';
        $dados['ativo'] = true;
        return $this->insert($dados);
    }

    /**
     * Atualiza profissional
     */
    public function atualizar(int $id, array $dados): bool
    {
        $dados['tipo_usuario'] = 'PROFISSIONAL';
        return $this->update("id = {$id} AND tipo_usuario = 'PROFISSIONAL'", $dados);
    }

    /**
     * Soft delete do profissional
     */
    public function remover(int $id): bool
    {
        return $this->update(
            "id = {$id} AND tipo_usuario = 'PROFISSIONAL'",
            ['ativo' => 0, 'deletado_em' => date('Y-m-d H:i:s')]
        );
    }

    /**
     * Vincula profissional a estabelecimento
     */
    public function vincularEstabelecimento(int $profissional_id, int $estabelecimento_id): int
    {
        $dados = [
            'profissional_id' => $profissional_id,
            'estabelecimento_id' => $estabelecimento_id,
            'data_inicio' => date('Y-m-d'),
            'ativo' => 1
        ];

        $stmt = $this->execute(
            "INSERT INTO profissional_estabelecimento (profissional_id, estabelecimento_id, data_inicio) 
             VALUES (?, ?, CURDATE())",
            [$profissional_id, $estabelecimento_id]
        );

        return $this->connection->lastInsertId();
    }

    /**
     * Remove vínculo profissional-estabelecimento
     */
    public function desvincularEstabelecimento(int $profissional_id, int $estabelecimento_id): bool
    {
        $stmt = $this->execute(
            "UPDATE profissional_estabelecimento 
             SET data_saida = CURDATE() 
             WHERE profissional_id = ? AND estabelecimento_id = ?",
            [$profissional_id, $estabelecimento_id]
        );

        return $stmt->rowCount() > 0;
    }

    /**
     * Total de profissionais ativos
     */
    public function total(): int
    {
        $stmt = $this->execute("SELECT COUNT(*) as total FROM usuario WHERE tipo_usuario = 'PROFISSIONAL' AND ativo = 1");
        return (int) $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
    }
}
