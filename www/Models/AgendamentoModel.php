<?php

namespace Models;

use Models\Database;

class AgendamentoModel extends Database
{
    public function __construct()
    {
        parent::__construct('agendamentos');
    }

    /**
     * Lista agendamentos de uma data específica (padrão: hoje)
     */
    public function listarPorData(string $data = ''): array
    {
        if (empty($data)) $data = date('Y-m-d');

        $stmt = $this->execute(
            "SELECT a.*,
                    c.cliente_nome, c.cliente_telefone,
                    s.servico_nome, s.servico_duracao, s.servico_preco,
                    u.usuario_nome AS profissional_nome
             FROM agendamentos a
             INNER JOIN clientes c  ON a.cliente_id  = c.cliente_id
             INNER JOIN servicos s  ON a.servico_id  = s.servico_id
             INNER JOIN usuarios u  ON a.usuario_id  = u.usuario_id
             WHERE a.agendamento_data = ?
             ORDER BY a.agendamento_hora ASC",
            [$data]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Lista agendamentos de uma data para um profissional específico (usuario_id)
     */
    public function listarPorDataProfissional(string $data, int $usuarioId): array
    {
        $stmt = $this->execute(
            "SELECT a.*,
                    c.cliente_nome, c.cliente_telefone,
                    s.servico_nome, s.servico_duracao, s.servico_preco,
                    u.usuario_nome AS profissional_nome
             FROM agendamentos a
             INNER JOIN clientes c  ON a.cliente_id  = c.cliente_id
             INNER JOIN servicos s  ON a.servico_id  = s.servico_id
             INNER JOIN usuarios u  ON a.usuario_id  = u.usuario_id
             WHERE a.agendamento_data = ? AND a.usuario_id = ?
             ORDER BY a.agendamento_hora ASC",
            [$data, $usuarioId]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Lista agendamentos de uma data para um cliente (mapeado pelo usuario_id via e-mail do usuário = e-mail do cliente)
     * OBS: idealmente deve haver FK clientes.cliente_usuario_id; esta é uma estratégia provisória.
     */
    public function listarPorDataCliente(string $data, int $usuarioId): array
    {
        // Primeiro tenta pelo vínculo cliente_usuario_id
        try {
            $stmt = $this->execute(
                "SELECT a.*,
                        c.cliente_nome, c.cliente_telefone,
                        s.servico_nome, s.servico_duracao, s.servico_preco,
                        u.usuario_nome AS profissional_nome
                 FROM agendamentos a
                 INNER JOIN clientes c  ON a.cliente_id  = c.cliente_id
                 INNER JOIN servicos s  ON a.servico_id  = s.servico_id
                 INNER JOIN usuarios u  ON a.usuario_id  = u.usuario_id
                 WHERE a.agendamento_data = ? AND c.cliente_usuario_id = ?
                 ORDER BY a.agendamento_hora ASC",
                [$data, $usuarioId]
            );
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            // Fallback: mapear por e-mail (ucli.email = c.email)
            $stmt = $this->execute(
                "SELECT a.*,
                        c.cliente_nome, c.cliente_telefone,
                        s.servico_nome, s.servico_duracao, s.servico_preco,
                        u.usuario_nome AS profissional_nome
                 FROM agendamentos a
                 INNER JOIN clientes c  ON a.cliente_id  = c.cliente_id
                 INNER JOIN servicos s  ON a.servico_id  = s.servico_id
                 INNER JOIN usuarios u  ON a.usuario_id  = u.usuario_id
                 INNER JOIN usuarios ucli ON ucli.usuario_email = c.cliente_email
                 WHERE a.agendamento_data = ? AND ucli.usuario_id = ?
                 ORDER BY a.agendamento_hora ASC",
                [$data, $usuarioId]
            );
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
    }

    /**
     * Lista próximos agendamentos do cliente (a partir de agora)
     */
    public function listarProximosPorCliente(int $clienteId): array
    {
        $stmt = $this->execute(
            "SELECT a.*,
                    c.cliente_nome, c.cliente_telefone,
                    s.servico_nome, s.servico_duracao, s.servico_preco,
                    u.usuario_nome AS profissional_nome
             FROM agendamentos a
             INNER JOIN clientes c ON a.cliente_id = c.cliente_id
             INNER JOIN servicos s ON a.servico_id = s.servico_id
             INNER JOIN usuarios u ON a.usuario_id = u.usuario_id
             WHERE a.cliente_id = ?
               AND TIMESTAMP(a.agendamento_data, a.agendamento_hora) >= NOW()
             ORDER BY a.agendamento_data ASC, a.agendamento_hora ASC",
            [$clienteId]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Lista histórico de agendamentos (datas passadas) do cliente
     */
    public function listarHistoricoPorCliente(int $clienteId): array
    {
        $stmt = $this->execute(
            "SELECT a.*,
                    c.cliente_nome, c.cliente_telefone,
                    s.servico_nome, s.servico_duracao, s.servico_preco,
                    u.usuario_nome AS profissional_nome
             FROM agendamentos a
             INNER JOIN clientes c ON a.cliente_id = c.cliente_id
             INNER JOIN servicos s ON a.servico_id = s.servico_id
             INNER JOIN usuarios u ON a.usuario_id = u.usuario_id
             WHERE a.cliente_id = ?
               AND TIMESTAMP(a.agendamento_data, a.agendamento_hora) < NOW()
             ORDER BY a.agendamento_data DESC, a.agendamento_hora DESC",
            [$clienteId]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Busca agendamento por ID garantindo vínculo com o cliente
     */
    public function buscarPorIdDoCliente(int $id, int $clienteId): ?array
    {
        $stmt = $this->execute(
            "SELECT a.*,
                    c.cliente_nome, c.cliente_telefone,
                    s.servico_nome, s.servico_duracao, s.servico_preco,
                    u.usuario_nome AS profissional_nome
             FROM agendamentos a
             INNER JOIN clientes c ON a.cliente_id = c.cliente_id
             INNER JOIN servicos s ON a.servico_id = s.servico_id
             INNER JOIN usuarios u ON a.usuario_id = u.usuario_id
             WHERE a.agendamento_id = ? AND a.cliente_id = ?
             LIMIT 1",
            [$id, $clienteId]
        );
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Busca agendamento por ID (com joins)
     */
    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->execute(
            "SELECT a.*,
                    c.cliente_nome, c.cliente_telefone,
                    s.servico_nome, s.servico_duracao, s.servico_preco,
                    u.usuario_nome AS profissional_nome
             FROM agendamentos a
             INNER JOIN clientes c  ON a.cliente_id  = c.cliente_id
             INNER JOIN servicos s  ON a.servico_id  = s.servico_id
             INNER JOIN usuarios u  ON a.usuario_id  = u.usuario_id
             WHERE a.agendamento_id = ? LIMIT 1",
            [$id]
        );
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Salva novo agendamento
     */
    public function salvar(array $dados): int
    {
        return $this->insert($dados);
    }

    /**
     * Atualiza status do agendamento
     */
    public function atualizarStatus(int $id, string $status): bool
    {
        return $this->update("agendamento_id = {$id}", ['agendamento_status' => $status]);
    }

    /**
     * Atualiza agendamento completo
     */
    public function atualizar(int $id, array $dados): bool
    {
        return $this->update("agendamento_id = {$id}", $dados);
    }

    /**
     * Remove agendamento
     */
    public function remover(int $id): bool
    {
        return $this->delete("agendamento_id = {$id}");
    }

    /**
     * Total de agendamentos de hoje
     */
    public function totalHoje(): int
    {
        $stmt = $this->execute(
            "SELECT COUNT(*) as total FROM agendamentos WHERE agendamento_data = CURDATE()"
        );
        return (int) $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
    }

    /**
     * Receita do dia atual (somente concluídos)
     */
    public function receitaHoje(): float
    {
        $stmt = $this->execute(
            "SELECT COALESCE(SUM(s.servico_preco), 0) as total
             FROM agendamentos a
             INNER JOIN servicos s ON a.servico_id = s.servico_id
             WHERE a.agendamento_data = CURDATE()
               AND a.agendamento_status = 'concluido'"
        );
        return (float) $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
    }

    /**
     * Receita do mês atual (somente concluídos)
     */
    public function receitaMes(): float
    {
        $stmt = $this->execute(
            "SELECT COALESCE(SUM(s.servico_preco), 0) as total
             FROM agendamentos a
             INNER JOIN servicos s ON a.servico_id = s.servico_id
             WHERE MONTH(a.agendamento_data) = MONTH(CURDATE())
               AND YEAR(a.agendamento_data)  = YEAR(CURDATE())
               AND a.agendamento_status = 'concluido'"
        );
        return (float) $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
    }
}
