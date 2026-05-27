<?php
namespace Models;

use Models\Database;

class EstabelecimentoModel extends Database
{
    public function __construct()
    {
        parent::__construct('estabelecimento');
    }

    /**
     * Lista todos os estabelecimentos ativos
     */
    public function listar(string $busca = ''): array
    {
        if (!empty($busca)) {
            $stmt = $this->execute(
                "SELECT id, administrador_id, nome_fantasia, cnpj_opcional, ativo
                 FROM estabelecimento
                 WHERE ativo = 1 AND nome_fantasia LIKE ?
                 ORDER BY nome_fantasia ASC",
                ["%{$busca}%"]
            );
        } else {
            $stmt = $this->execute(
                "SELECT id, administrador_id, nome_fantasia, cnpj_opcional, ativo 
                 FROM estabelecimento WHERE ativo = 1 ORDER BY nome_fantasia ASC"
            );
        }
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

}






?>