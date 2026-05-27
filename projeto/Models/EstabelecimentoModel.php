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
    /**
     * Salva novo estabelecimento
     */
    public function salvar(array $dados): int
    {
        $dados['ativo'] = $dados['ativo'] ?? 1;
        return $this->insert($dados);
    }

    /**
     * Atualiza estabelecimento
     */
    public function atualizar(int $id, array $dados): bool
    {
        return $this->update("id = {$id}", $dados);
    }

    /**
     * Soft delete do estabelecimento
     */
    public function remover(int $id): bool
    {
        return $this->update("id = {$id}", ['ativo' => 0, 'deletado_em' => date('Y-m-d H:i:s')]);
    }
    /**
     * Total de estabelecimentos ativos
     */
    public function total(): int
    {
        $stmt = $this->execute("SELECT COUNT(*) as total FROM estabelecimento WHERE ativo = 1");
        return (int) $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
    }
    /**
     * Busca horários de funcionamento
     */
    public function buscarHorarios(int $estabelecimento_id): array
    {
        $stmt = $this->execute(
            "SELECT dia_semana, hora_abertura, hora_fechamento, ativo 
             FROM horario_funcionamento 
             WHERE estabelecimento_id = ? ORDER BY FIELD(dia_semana, 'SEGUNDA', 'TERCA', 'QUARTA', 'QUINTA', 'SEXTA', 'SABADO', 'DOMINGO')",
            [$estabelecimento_id]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    /**
     * Verifica se está aberto em um dia/hora específico
     */
    public function estaAberto(int $estabelecimento_id, string $dataHora): bool
    {
        $dia = [
            'domingo' => 'DOMINGO',
            'monday' => 'SEGUNDA',
            'tuesday' => 'TERCA',
            'wednesday' => 'QUARTA',
            'thursday' => 'QUINTA',
            'friday' => 'SEXTA',
            'saturday' => 'SABADO'
        ];
        $diaSemana = $dia[strtolower(date('l', strtotime($dataHora)))];
        $hora = date('H:i', strtotime($dataHora));

        $stmt = $this->execute(
            "SELECT COUNT(*) as total FROM horario_funcionamento 
             WHERE estabelecimento_id = ? AND dia_semana = ? AND ativo = 1
             AND hora_abertura <= ? AND hora_fechamento > ?",
            [$estabelecimento_id, $diaSemana, $hora, $hora]
        );

        return (int) $stmt->fetch(\PDO::FETCH_ASSOC)['total'] > 0;
    }
}






