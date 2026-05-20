<!-- Dashboard: Cards de estatísticas -->
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted mb-0">Agendamentos Hoje</h6>
                    <div class="bg-primary bg-opacity-10 text-primary rounded px-2 py-1">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-0"><?php echo $agendamentos_hoje; ?></h2>
                <small class="text-muted">Agendados para hoje</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted mb-0">Receita do Dia</h6>
                    <div class="bg-success bg-opacity-10 text-success rounded px-2 py-1">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-0"><?php echo formatarDinheiro($receita_hoje); ?></h2>
                <small class="text-muted">Serviços concluídos hoje</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted mb-0">Clientes Cadastrados</h6>
                    <div class="bg-info bg-opacity-10 text-info rounded px-2 py-1">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-0"><?php echo $total_clientes; ?></h2>
                <small class="text-muted">No total</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted mb-0">Receita do Mês</h6>
                    <div class="bg-warning bg-opacity-10 text-warning rounded px-2 py-1">
                        <i class="bi bi-graph-up"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-0"><?php echo formatarDinheiro($receita_mes); ?></h2>
                <small class="text-muted">Mês atual (concluídos)</small>
            </div>
        </div>
    </div>
</div>

<!-- Próximos atendimentos -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold">Agendamentos de Hoje</h5>
        <a href="<?php echo base_url('agendamentos'); ?>" class="btn btn-sm btn-outline-primary rounded-pill">Ver todos</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Horário</th>
                        <th>Cliente</th>
                        <th>Serviço</th>
                        <th>Profissional</th>
                        <th class="text-end pe-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($proximos)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                Nenhum agendamento para hoje.
                                <a href="<?php echo base_url('agendamentos/novo'); ?>">Criar agendamento</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php
                        $badges = [
                            'aguardando'   => 'bg-warning text-dark',
                            'confirmado'   => 'bg-success',
                            'em_andamento' => 'bg-info text-dark',
                            'concluido'    => 'bg-primary',
                            'cancelado'    => 'bg-danger',
                        ];
                        $labels = [
                            'aguardando'   => 'Aguardando',
                            'confirmado'   => 'Confirmado',
                            'em_andamento' => 'Em Andamento',
                            'concluido'    => 'Concluído',
                            'cancelado'    => 'Cancelado',
                        ];
                        foreach ($proximos as $ag):
                        $st = $ag['agendamento_status'];
                        ?>
                        <tr>
                            <td class="ps-4 fw-semibold text-dark">
                                <?php echo date('H:i', strtotime($ag['agendamento_hora'])); ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle text-center me-2 fw-bold"
                                         style="width:32px;height:32px;line-height:32px;font-size:0.75rem;">
                                        <?php echo mb_strtoupper(mb_substr($ag['cliente_nome'], 0, 2)); ?>
                                    </div>
                                    <?php echo htmlspecialchars($ag['cliente_nome']); ?>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($ag['servico_nome']); ?></td>
                            <td><?php echo htmlspecialchars($ag['profissional_nome']); ?></td>
                            <td class="text-end pe-4">
                                <span class="badge <?php echo $badges[$st] ?? 'bg-secondary'; ?> rounded-pill">
                                    <?php echo $labels[$st] ?? $st; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
