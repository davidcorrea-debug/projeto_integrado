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
    /**
     * Busca estabelecimento por ID
     */
    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->execute(
            "SELECT e.*, ee.cep, ee.rua, ee.bairro, ee.numero, ee.complemento, c.nome as cidade, st.uf
             FROM estabelecimento e
             LEFT JOIN endereco_estabelecimento ee ON e.id = ee.estabelecimento_id
             LEFT JOIN cidade c ON ee.cidade_id = c.id
             LEFT JOIN estado st ON c.estado_id = st.id
             WHERE e.id = ? LIMIT 1",
            [$id]
        );
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }


}






?>