<?php

namespace Models;

use Models\Database;

class ServicoModel extends Database
{
    public function __construct()
    {
        parent::__construct('servicos');
    }

    /**
     * Lista todos os serviços com o nome da categoria
     */
    public function listar(string $busca = '', string $categoriaId = '', string $ativo = ''): array
    {
        $sql = "SELECT s.*, c.categoria_nome
                FROM servicos s
                INNER JOIN categorias c ON s.categoria_id = c.categoria_id
                WHERE 1=1";
        $params = [];

        if (!empty($busca)) {
            $sql .= " AND s.servico_nome LIKE ?";
            $params[] = "%{$busca}%";
        }
        if (!empty($categoriaId)) {
            $sql .= " AND s.categoria_id = ?";
            $params[] = $categoriaId;
        }
        if ($ativo !== '') {
            $sql .= " AND s.servico_ativo = ?";
            $params[] = $ativo;
        }

        $sql .= " ORDER BY s.servico_nome ASC";
        $stmt = $this->execute($sql, $params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Retorna contagem de serviços por categoria (total e ativos)
     */
    public function contagemPorCategoria(): array
    {
        $stmt = $this->execute(
            "SELECT categoria_id,
                    COUNT(*) AS total,
                    SUM(CASE WHEN servico_ativo = 1 THEN 1 ELSE 0 END) AS ativos
             FROM servicos
             GROUP BY categoria_id"
        );

        $map = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $id = (int)($row['categoria_id'] ?? 0);
            if (!$id) {
                continue;
            }
            $map[$id] = [
                'total'  => (int)($row['total'] ?? 0),
                'ativos' => (int)($row['ativos'] ?? 0),
            ];
        }

        return $map;
    }

    /**
     * Busca serviço por ID (com categoria)
     */
    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->execute(
            "SELECT s.*, c.categoria_nome
             FROM servicos s
             INNER JOIN categorias c ON s.categoria_id = c.categoria_id
             WHERE s.servico_id = ? LIMIT 1",
            [$id]
        );
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Lista apenas serviços ativos (para select em agendamento)
     */
    public function listarAtivos(): array
    {
        $stmt = $this->execute(
            "SELECT servico_id, servico_nome, servico_preco, servico_duracao, categoria_id
             FROM servicos WHERE servico_ativo = 1 ORDER BY servico_nome ASC"
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Salva novo serviço
     */
    public function salvar(array $dados): int
    {
        return $this->insert($dados);
    }

    /**
     * Atualiza serviço
     */
    public function atualizar(int $id, array $dados): bool
    {
        return $this->update("servico_id = {$id}", $dados);
    }

    /**
     * Atualiza flag de serviço ativo/inativo
     */
    public function atualizarStatusAtivo(int $id, bool $ativo): bool
    {
        return $this->update("servico_id = {$id}", ['servico_ativo' => $ativo ? 1 : 0]);
    }

    /**
     * Desativa serviço sem remover o registro
     */
    public function desativar(int $id): bool
    {
        return $this->atualizarStatusAtivo($id, false);
    }

    /**
     * Reativa serviço previamente desativado
     */
    public function ativar(int $id): bool
    {
        return $this->atualizarStatusAtivo($id, true);
    }

    /**
     * Mantido por compatibilidade: realiza desativação lógica
     */
    public function remover(int $id): bool
    {
        return $this->desativar($id);
    }

    /**
     * Total de serviços ativos
     */
    public function totalAtivos(): int
    {
        $stmt = $this->execute("SELECT COUNT(*) as total FROM servicos WHERE servico_ativo = 1");
        return (int) $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
    }
}
