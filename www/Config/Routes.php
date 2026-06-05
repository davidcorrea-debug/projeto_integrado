<?php

return [
    // Rota inicial
    '/'                             => ['AuthController', 'login'],

    // Auth
    '/login'                        => ['AuthController', 'login'],
    '/logout'                       => ['AuthController', 'logout'],
    '/auth/authenticate'            => ['AuthController', 'authenticate'],
    '/forgot-password'              => ['AuthController', 'forgot'],
    '/auth/send-reset'              => ['AuthController', 'sendReset'],
    '/reset-password'               => ['AuthController', 'resetForm'],
    '/auth/reset'                   => ['AuthController', 'reset'],
    '/cadastro'                     => ['RegisterController', 'form'],
    '/cadastro/salvar'              => ['RegisterController', 'salvar'],

    // Dashboard
    '/dashboard'                    => ['DashboardController', 'index'],

    // Agendamentos
    '/agendamentos'                 => ['AgendamentoController', 'index'],
    '/agendamentos/novo'            => ['AgendamentoController', 'novo'],
    '/agendamentos/salvar'          => ['AgendamentoController', 'salvar'],
    '/agendamentos/status/{id}'     => ['AgendamentoController', 'status'],
    '/agendamentos/excluir/{id}'    => ['AgendamentoController', 'excluir'],

    // Portal do Cliente - Agendamentos
    '/cliente/agendamentos'                     => ['ClienteAgendamentoController', 'index'],
    '/cliente/agendamentos/novo'                => ['ClienteAgendamentoController', 'novo'],
    '/cliente/agendamentos/salvar'              => ['ClienteAgendamentoController', 'salvar'],
    '/cliente/agendamentos/{id}/editar'         => ['ClienteAgendamentoController', 'editar'],
    '/cliente/agendamentos/{id}/atualizar'      => ['ClienteAgendamentoController', 'atualizar'],
    '/cliente/agendamentos/{id}/cancelar'       => ['ClienteAgendamentoController', 'cancelar'],

    // Serviços
    '/servicos'                     => ['ServicoController', 'index'],
    '/servicos/novo'                => ['ServicoController', 'novo'],
    '/servicos/salvar'              => ['ServicoController', 'salvar'],
    '/servicos/editar/{id}'         => ['ServicoController', 'editar'],
    '/servicos/atualizar/{id}'      => ['ServicoController', 'atualizar'],
    '/servicos/excluir/{id}'        => ['ServicoController', 'excluir'],

    // Clientes
    '/clientes'                     => ['ClienteController', 'index'],
    '/clientes/novo'                => ['ClienteController', 'novo'],
    '/clientes/salvar'              => ['ClienteController', 'salvar'],
    '/clientes/editar/{id}'         => ['ClienteController', 'editar'],
    '/clientes/atualizar/{id}'      => ['ClienteController', 'atualizar'],
    '/clientes/excluir/{id}'        => ['ClienteController', 'excluir'],

    // Configurações de conta
    '/configuracoes'                => ['ConfiguracaoController', 'editar'],
    '/configuracoes/salvar'         => ['ConfiguracaoController', 'atualizar'],

    // Profissionais
    '/profissionais'                => ['ProfissionalController', 'index'],
    '/profissionais/novo'           => ['ProfissionalController', 'novo'],
    '/profissionais/salvar'         => ['ProfissionalController', 'salvar'],
];

