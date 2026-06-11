<?php

namespace Models;

use Models\Database;

class AgendamentoModel extends Database
{
    private HorarioTrabalhoModel $horarioModel;

    public function __construct()
    {
        parent::__construct('agendamentos');
        $this->horarioModel = new HorarioTrabalhoModel($this->getConnection());
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
               AND a.agendamento_status <> 'cancelado'
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
     * Verifica se já existe agendamento no mesmo horário para o profissional
     */
    public function existeConflitoHorario(int $usuarioId, string $data, string $hora, ?int $ignorarId = null, ?int $duracaoMinutos = null): bool
    {
        $duracao = $duracaoMinutos ?? 30;
        return $this->temConflitoIntervalo($usuarioId, $data, $hora, $duracao, $ignorarId);
    }

    public function temConflitoIntervalo(int $usuarioId, string $data, string $horaInicio, int $duracaoMinutos, ?int $ignorarId = null): bool
    {
        $duracao = max(5, $duracaoMinutos);

        try {
            $inicio = new \DateTimeImmutable($data . ' ' . $horaInicio);
        } catch (\Throwable $e) {
            return true;
        }

        $fim = $inicio->add(new \DateInterval('PT' . $duracao . 'M'));

        if (!$this->estaDentroDoExpediente($usuarioId, $inicio, $fim)) {
            return true;
        }

        $intervalos = $this->obterAgendaDoDia($usuarioId, $data, $ignorarId);

        foreach ($intervalos as $intervalo) {
            if ($inicio < $intervalo['fim'] && $fim > $intervalo['inicio']) {
                return true;
            }
        }

        return false;
    }

    public function calcularHorariosDisponiveis(int $usuarioId, string $data, int $duracaoMinutos, int $intervaloMinutos = 15, ?int $ignorarId = null): array
    {
        $duracao = max(5, $duracaoMinutos);
        $passo = max(5, $intervaloMinutos);

        $intervaloDuracao = new \DateInterval('PT' . $duracao . 'M');
        $intervaloPasso   = new \DateInterval('PT' . $passo . 'M');
        $hoje             = new \DateTimeImmutable();

        $janelasExpediente = $this->obterJanelasExpediente($usuarioId, $data);
        if (empty($janelasExpediente)) {
            return [];
        }

        $intervalosOcupados = $this->obterAgendaDoDia($usuarioId, $data, $ignorarId);
        $disponiveis = [];

        foreach ($janelasExpediente as $janela) {
            try {
                $inicioJanela = new \DateTimeImmutable($data . ' ' . $janela['inicio']);
                $fimJanela    = new \DateTimeImmutable($data . ' ' . $janela['fim']);
            } catch (\Throwable $e) {
                continue;
            }

            if ($inicioJanela >= $fimJanela) {
                continue;
            }

            $inicioSlot = $this->alinharAoIntervalo($inicioJanela, $passo);

            if ($inicioJanela->format('Y-m-d') === $hoje->format('Y-m-d') && $hoje > $inicioSlot) {
                $inicioSlot = $this->alinharAoIntervalo($hoje, $passo);
                if ($inicioSlot < $inicioJanela) {
                    $inicioSlot = $inicioJanela;
                }
            }

            for ($inicio = $inicioSlot; $inicio < $fimJanela; $inicio = $inicio->add($intervaloPasso)) {
                $fimSlot = $inicio->add($intervaloDuracao);

                if ($fimSlot > $fimJanela) {
                    break;
                }

                if ($inicio < $hoje && $inicioJanela->format('Y-m-d') === $hoje->format('Y-m-d')) {
                    continue;
                }

                $conflito = false;
                foreach ($intervalosOcupados as $ocupado) {
                    if ($inicio < $ocupado['fim'] && $fimSlot > $ocupado['inicio']) {
                        $conflito = true;
                        break;
                    }
                }

                if (!$conflito && $this->estaDentroDoExpediente($usuarioId, $inicio, $fimSlot)) {
                    $disponiveis[] = $inicio->format('H:i');
                }
            }
        }

        return array_values(array_unique($disponiveis));
    }

    private function obterAgendaDoDia(int $usuarioId, string $data, ?int $ignorarId = null): array
    {
        $sql = "SELECT a.agendamento_id, a.agendamento_hora, a.agendamento_status, s.servico_duracao
                FROM agendamentos a
                INNER JOIN servicos s ON s.servico_id = a.servico_id
                WHERE a.usuario_id = ?
                  AND a.agendamento_data = ?
                  AND a.agendamento_status <> 'cancelado'";

        $params = [$usuarioId, $data];

        if (!empty($ignorarId)) {
            $sql    .= " AND a.agendamento_id <> ?";
            $params[] = $ignorarId;
        }

        $sql .= " ORDER BY a.agendamento_hora ASC";

        $stmt = $this->execute($sql, $params);
        $registros = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $intervalos = [];
        foreach ($registros as $row) {
            $horaInicio = $row['agendamento_hora'] ?? null;
            if (!$horaInicio) {
                continue;
            }

            try {
                $inicio = new \DateTimeImmutable($data . ' ' . $horaInicio);
            } catch (\Throwable $e) {
                continue;
            }

            $duracao = (int)($row['servico_duracao'] ?? 0);
            if ($duracao <= 0) {
                $duracao = 30;
            }

            $fim = $inicio->add(new \DateInterval('PT' . $duracao . 'M'));
            $intervalos[] = [
                'inicio' => $inicio,
                'fim'    => $fim,
                'status' => $row['agendamento_status'] ?? 'aguardando',
            ];
        }

        return $intervalos;
    }

    private function alinharAoIntervalo(\DateTimeImmutable $momento, int $intervaloMinutos): \DateTimeImmutable
    {
        $momento = $momento->setTime((int)$momento->format('H'), (int)$momento->format('i'), 0);
        $modulo = (int)$momento->format('i') % $intervaloMinutos;

        if ($modulo !== 0) {
            $momento = $momento->add(new \DateInterval('PT' . ($intervaloMinutos - $modulo) . 'M'));
        }

        return $momento->setTime((int)$momento->format('H'), (int)$momento->format('i'), 0);
    }

    private function obterJanelasExpediente(int $usuarioId, string $data): array
    {
        try {
            $diaSemana = (int)(new \DateTimeImmutable($data))->format('w');
        } catch (\Throwable $e) {
            return $this->expedientePadrao();
        }

        $registro = $this->horarioModel->buscarPorUsuarioEDia($usuarioId, $diaSemana);

        if (!$registro) {
            return $this->expedientePadrao();
        }

        if ((int)($registro['ativo'] ?? 1) !== 1) {
            return [];
        }

        $inicio = $registro['hora_inicio'] ?? null;
        $fim    = $registro['hora_fim'] ?? null;

        if (!$inicio || !$fim || strcmp($inicio, $fim) >= 0) {
            return [];
        }

        return [[
            'inicio' => $inicio,
            'fim'    => $fim,
        ]];
    }

    private function expedientePadrao(): array
    {
        return [[
            'inicio' => '08:00:00',
            'fim'    => '20:00:00',
        ]];
    }

    private function estaDentroDoExpediente(int $usuarioId, \DateTimeImmutable $inicio, \DateTimeImmutable $fim): bool
    {
        $janelas = $this->obterJanelasExpediente($usuarioId, $inicio->format('Y-m-d'));
        if (empty($janelas)) {
            return false;
        }

        foreach ($janelas as $janela) {
            try {
                $inicioJanela = new \DateTimeImmutable($inicio->format('Y-m-d') . ' ' . $janela['inicio']);
                $fimJanela    = new \DateTimeImmutable($inicio->format('Y-m-d') . ' ' . $janela['fim']);
            } catch (\Throwable $e) {
                continue;
            }

            if ($inicio >= $inicioJanela && $fim <= $fimJanela) {
                return true;
            }
        }

        return false;
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

    public function totalHojePorProfissional(int $usuarioId): int
    {
        $stmt = $this->execute(
            "SELECT COUNT(*) as total
             FROM agendamentos
             WHERE agendamento_data = CURDATE()
               AND usuario_id = ?",
            [$usuarioId]
        );

        return (int)($stmt->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);
    }

    public function receitaHojePorProfissional(int $usuarioId): float
    {
        $stmt = $this->execute(
            "SELECT COALESCE(SUM(s.servico_preco), 0) as total
             FROM agendamentos a
             INNER JOIN servicos s ON a.servico_id = s.servico_id
             WHERE a.agendamento_data = CURDATE()
               AND a.usuario_id = ?
               AND a.agendamento_status = 'concluido'",
            [$usuarioId]
        );

        return (float)($stmt->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0.0);
    }

    public function receitaMesPorProfissional(int $usuarioId): float
    {
        $stmt = $this->execute(
            "SELECT COALESCE(SUM(s.servico_preco), 0) as total
             FROM agendamentos a
             INNER JOIN servicos s ON a.servico_id = s.servico_id
             WHERE MONTH(a.agendamento_data) = MONTH(CURDATE())
               AND YEAR(a.agendamento_data)  = YEAR(CURDATE())
               AND a.usuario_id = ?
               AND a.agendamento_status = 'concluido'",
            [$usuarioId]
        );

        return (float)($stmt->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0.0);
    }

    public function totalClientesPorProfissional(int $usuarioId): int
    {
        $stmt = $this->execute(
            "SELECT COUNT(DISTINCT cliente_id) as total
             FROM agendamentos
             WHERE usuario_id = ?",
            [$usuarioId]
        );

        return (int)($stmt->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);
    }

    public function totalServicosPorProfissional(int $usuarioId): int
    {
        $stmt = $this->execute(
            "SELECT COUNT(DISTINCT servico_id) as total
             FROM agendamentos
             WHERE usuario_id = ?",
            [$usuarioId]
        );

        return (int)($stmt->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);
    }
}
