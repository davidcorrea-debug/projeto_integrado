<?php

return [
    // Rota inicial
    '/'                             => ['AuthController', 'login'],

    // Auth
    '/login'                        => ['AuthController', 'login'],
    '/logout'                       => ['AuthController', 'logout'],
    '/auth/authenticate'            => ['AuthController', 'authenticate'],

    // Dashboard
    '/dashboard'                    => ['DashboardController', 'index'],

    // Agendamentos
    '/agendamentos'                 => ['AgendamentoController', 'index'],
    '/agendamentos/novo'            => ['AgendamentoController', 'novo'],
    '/agendamentos/salvar'          => ['AgendamentoController', 'salvar'],
    '/agendamentos/status/{id}'     => ['AgendamentoController', 'status'],
    '/agendamentos/excluir/{id}'    => ['AgendamentoController', 'excluir'],

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
];

