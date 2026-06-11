<?php

namespace Models;

class HorarioTrabalhoModel extends Database
{
    public function __construct(?\PDO $connection = null)
    {
        parent::__construct('profissionais_horarios', $connection);
        $this->criarTabelaSeNecessario();
    }

    private function criarTabelaSeNecessario(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS profissionais_horarios (
            horario_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT UNSIGNED NOT NULL,
            dia_semana TINYINT UNSIGNED NOT NULL,
            hora_inicio TIME NOT NULL,
            hora_fim TIME NOT NULL,
            ativo TINYINT(1) NOT NULL DEFAULT 1,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_usuario_dia (usuario_id, dia_semana),
            CONSTRAINT fk_profissionais_horarios_usuario
                FOREIGN KEY (usuario_id) REFERENCES usuarios (usuario_id)
                ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $this->execute($sql);
    }

    public function listarPorUsuario(int $usuarioId): array
    {
        $stmt = $this->execute(
            "SELECT horario_id, usuario_id, dia_semana, hora_inicio, hora_fim, ativo
             FROM profissionais_horarios WHERE usuario_id = ? ORDER BY dia_semana ASC",
            [$usuarioId]
        );

        $horarios = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $dia = (int)($row['dia_semana'] ?? 0);
            $horarios[$dia] = [
                'horario_id' => (int)($row['horario_id'] ?? 0),
                'usuario_id' => (int)($row['usuario_id'] ?? 0),
                'dia_semana' => $dia,
                'hora_inicio' => $row['hora_inicio'] ?? '08:00:00',
                'hora_fim' => $row['hora_fim'] ?? '20:00:00',
                'ativo' => (int)($row['ativo'] ?? 0),
            ];
        }

        return $horarios;
    }

    public function buscarPorUsuarioEDia(int $usuarioId, int $diaSemana): ?array
    {
        $stmt = $this->execute(
            "SELECT horario_id, usuario_id, dia_semana, hora_inicio, hora_fim, ativo
             FROM profissionais_horarios WHERE usuario_id = ? AND dia_semana = ? LIMIT 1",
            [$usuarioId, $diaSemana]
        );

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function salvarOuAtualizar(int $usuarioId, int $diaSemana, string $horaInicio, string $horaFim, bool $ativo = true): void
    {
        $this->execute(
            "INSERT INTO profissionais_horarios (usuario_id, dia_semana, hora_inicio, hora_fim, ativo)
             VALUES (?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE hora_inicio = VALUES(hora_inicio), hora_fim = VALUES(hora_fim), ativo = VALUES(ativo)",
            [$usuarioId, $diaSemana, $horaInicio, $horaFim, $ativo ? 1 : 0]
        );
    }

    public function removerPorUsuario(int $usuarioId): void
    {
        $this->execute("DELETE FROM profissionais_horarios WHERE usuario_id = ?", [$usuarioId]);
    }
}
