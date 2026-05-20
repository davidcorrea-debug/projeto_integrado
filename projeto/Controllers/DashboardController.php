<?php

namespace Controllers;

require_once("Models/Database.php");
require_once("Config/Helpers.php");
use Models\AgendamentoModel;
use Models\ClienteModel;
use Models\ServicoModel;

class DashboardController
{
    public function index(): void
    {
        $agendamentoModel = new AgendamentoModel();
        $clienteModel     = new ClienteModel();
        $servicoModel     = new ServicoModel();

        $data = [
            'pagina'             => 'Dashboard',
            'agendamentos_hoje'  => $agendamentoModel->totalHoje(),
            'receita_hoje'       => $agendamentoModel->receitaHoje(),
            'receita_mes'        => $agendamentoModel->receitaMes(),
            'total_clientes'     => $clienteModel->total(),
            'total_servicos'     => $servicoModel->totalAtivos(),
            'proximos'           => $agendamentoModel->listarPorData(date('Y-m-d')),
        ];

        view('dashboard/index', $data);
    }
}
