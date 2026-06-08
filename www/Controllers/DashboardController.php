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
        $perfil    = $_SESSION['usuario_perfil'] ?? '';
        $usuarioId = (int)($_SESSION['usuario_id'] ?? 0);
        $email     = $_SESSION['usuario_email'] ?? null;

        $agendamentoModel = new AgendamentoModel();
        $clienteModel     = new ClienteModel();

        if ($perfil === 'cliente') {
            if (!$usuarioId) {
                redirect('login');
            }

            $cliente = $clienteModel->buscarPorUsuarioId($usuarioId, $email);
            if (!$cliente) {
                $_SESSION['msg'] = msg('Não encontramos um cadastro de cliente vinculado à sua conta.', 'danger');
                redirect('configuracoes');
            }

            $proximos = $agendamentoModel->listarProximosPorCliente((int)$cliente['cliente_id']);
            $historico = $agendamentoModel->listarHistoricoPorCliente((int)$cliente['cliente_id']);

            $data = [
                'pagina'          => 'Resumo da Conta',
                'cliente'         => $cliente,
                'proximos'        => array_slice($proximos, 0, 4),
                'proximoPrincipal'=> $proximos[0] ?? null,
                'totalProximos'   => is_countable($proximos) ? count($proximos) : 0,
                'historico'       => array_slice($historico, 0, 5),
            ];

            view('dashboard/cliente', $data);
            return;
        }

        if ($perfil === 'profissional') {
            if (!$usuarioId) {
                redirect('login');
            }

            $data = [
                'pagina'             => 'Meu Painel',
                'agendamentos_hoje'  => $agendamentoModel->totalHojePorProfissional($usuarioId),
                'receita_hoje'       => $agendamentoModel->receitaHojePorProfissional($usuarioId),
                'receita_mes'        => $agendamentoModel->receitaMesPorProfissional($usuarioId),
                'total_clientes'     => $agendamentoModel->totalClientesPorProfissional($usuarioId),
                'total_servicos'     => $agendamentoModel->totalServicosPorProfissional($usuarioId),
                'proximos'           => $agendamentoModel->listarPorDataProfissional(date('Y-m-d'), $usuarioId),
                'painel_profissional'=> true,
            ];

            view('dashboard/index', $data);
            return;
        }

        $servicoModel = new ServicoModel();

        $data = [
            'pagina'             => 'Dashboard',
            'agendamentos_hoje'  => $agendamentoModel->totalHoje(),
            'receita_hoje'       => $agendamentoModel->receitaHoje(),
            'receita_mes'        => $agendamentoModel->receitaMes(),
            'total_clientes'     => $clienteModel->total(),
            'total_servicos'     => $servicoModel->totalAtivos(),
            'proximos'           => $agendamentoModel->listarPorData(date('Y-m-d')),
            'painel_profissional'=> false,
        ];

        view('dashboard/index', $data);
    }
}
