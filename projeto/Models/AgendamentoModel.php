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
