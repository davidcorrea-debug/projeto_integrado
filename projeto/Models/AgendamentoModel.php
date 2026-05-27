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
   public function listarPorData(int $estabelecimento_id, string $data = ''): array
    {
        if (empty($data)) $data = date('Y-m-d');

        $stmt = $this->execute(
            "SELECT a.id, a.data_hora_inicio, a.status, a.tempo_total_minutos, a.valor_total,
                    uc.nome as cliente_nome, uc.telefone as cliente_telefone,
                    up.nome as profissional_nome, up.email as profissional_email,
                    a.observacoes
             FROM agendamento a
             INNER JOIN usuario uc ON a.cliente_id = uc.id
             INNER JOIN usuario up ON a.profissional_id = up.id
             WHERE a.estabelecimento_id = ? AND DATE(a.data_hora_inicio) = ?
             ORDER BY a.data_hora_inicio ASC",
            [$estabelecimento_id, $data]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Busca agendamento por ID (com joins)
     */
    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->execute(
              "SELECT a.id, a.estabelecimento_id, a.cliente_id, a.profissional_id,
                    a.data_hora_inicio, a.tempo_total_minutos, a.valor_total, 
                    a.observacoes, a.status, a.cancelado_por, a.cancelado_motivo, a.cancelado_em,
                    uc.nome as cliente_nome, uc.telefone as cliente_telefone,
                    up.nome as profissional_nome, up.email as profissional_email
             FROM agendamento a
             INNER JOIN usuario uc ON a.cliente_id = uc.id
             INNER JOIN usuario up ON a.profissional_id = up.id
             WHERE a.id = ? LIMIT 1",
            [$id]
        );
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Lista agendamentos do cliente
     */
    public function listarPorCliente(int $cliente_id, string $status = 'AGENDADO'): array
    {
        $stmt = $this->execute(
            "SELECT a.id, a.data_hora_inicio, a.status, a.valor_total, a.tempo_total_minutos,
                    e.nome_fantasia as estabelecimento,
                    up.nome as profissional_nome
             FROM agendamento a
             INNER JOIN estabelecimento e ON a.estabelecimento_id = e.id
             INNER JOIN usuario up ON a.profissional_id = up.id
             WHERE a.cliente_id = ? AND a.status = ? AND e.ativo = 1
             ORDER BY a.data_hora_inicio DESC",
            [$cliente_id, $status]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Lista agendamentos do profissional
     */
    public function listarPorProfissional(int $profissional_id, string $data = ''): array
    {
        if (empty($data)) $data = date('Y-m-d');

        $stmt = $this->execute(
            "SELECT a.id, a.data_hora_inicio, a.status, a.valor_total,
                    uc.nome as cliente_nome, uc.telefone as cliente_telefone,
                    e.nome_fantasia as estabelecimento
             FROM agendamento a
             INNER JOIN usuario uc ON a.cliente_id = uc.id
             INNER JOIN estabelecimento e ON a.estabelecimento_id = e.id
             WHERE a.profissional_id = ? AND DATE(a.data_hora_inicio) = ?
             ORDER BY a.data_hora_inicio ASC",
            [$profissional_id, $data]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
/**
     * Verifica conflito de horários
     */
    public function verificarDisponibilidade(int $profissional_id, string $dataHoraInicio, int $duracao): bool
    {
        // Calcula hora final do agendamento
        $dataHoraFim = date('Y-m-d H:i:s', strtotime($dataHoraInicio) + ($duracao * 60));
        
        $stmt = $this->execute(
            "SELECT COUNT(*) as total FROM agendamento
             WHERE profissional_id = ? 
             AND status != 'CANCELADO'
             AND (
                (data_hora_inicio < ? AND DATE_ADD(data_hora_inicio, INTERVAL tempo_total_minutos MINUTE) > ?)
             )",
            [$profissional_id, $dataHoraFim, $dataHoraInicio]
        );
        
        return (int) $stmt->fetch(\PDO::FETCH_ASSOC)['total'] === 0;
    }

    /**
     * Salva novo agendamento
     */
    public function salvar(array $dados): int
    {
        $dados['status'] = $dados['status'] ?? 'AGENDADO';
        return $this->insert($dados);
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
