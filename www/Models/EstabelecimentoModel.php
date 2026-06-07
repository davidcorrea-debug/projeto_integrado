<?php

namespace Models;

class EstabelecimentoModel extends Database
{
    private const ID_PADRAO = 1;

    public function __construct(?\PDO $connection = null)
    {
        parent::__construct('estabelecimento', $connection);
    }

    public function obter(): ?array
    {
        $stmt = $this->execute(
            "SELECT id, nome, nome_fantasia, cnpj, telefone, email, endereco, cep, localizacao_url, instagram, facebook, site
             FROM estabelecimento
             ORDER BY id ASC
             LIMIT 1"
        );

        $dados = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $dados ?: null;
    }

    public function salvar(array $dados): bool
    {
        $payload = $this->filtrarCampos($dados);

        if (empty($payload)) {
            return false;
        }

        $registro = $this->obter();

        if ($registro) {
            return $this->update('id = ' . (int)($registro['id'] ?? self::ID_PADRAO), $payload);
        }

        $payload['id'] = self::ID_PADRAO;
        $this->insert($payload);

        return true;
    }

    private function filtrarCampos(array $dados): array
    {
        $permitidos = [
            'nome',
            'nome_fantasia',
            'cnpj',
            'telefone',
            'email',
            'endereco',
            'cep',
            'localizacao_url',
            'instagram',
            'facebook',
            'site',
        ];

        $resultado = [];
        foreach ($permitidos as $campo) {
            if (array_key_exists($campo, $dados)) {
                $valor = $dados[$campo];
                if (is_string($valor)) {
                    $valor = trim($valor);
                }
                $resultado[$campo] = $valor;
            }
        }

        return $resultado;
    }
}
