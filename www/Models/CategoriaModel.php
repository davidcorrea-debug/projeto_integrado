<?php

namespace Models;

use Models\Database;

class CategoriaModel extends Database
{
    public function __construct()
    {
        parent::__construct('categorias');
    }

    /**
     * Lista todas as categorias ativas
     */
    public function listar(): array
    {
        $stmt = $this->execute(
            "SELECT * FROM categorias WHERE categoria_ativo = 1 ORDER BY categoria_nome ASC"
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function buscarPorNome(string $nome): ?array
    {
        $stmt = $this->execute(
            "SELECT * FROM categorias WHERE LOWER(categoria_nome) = LOWER(?) LIMIT 1",
            [$nome]
        );
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function criar(string $nome): int
    {
        return (int)$this->insert([
            'categoria_nome'  => $nome,
            'categoria_ativo' => 1,
        ]);
    }
}
