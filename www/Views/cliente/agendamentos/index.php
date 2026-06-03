<?php if (!empty($msg)) echo $msg; ?>

<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
    <div>
        <h5 class="fw-semibold mb-1 text-dark">Olá, <?php echo htmlspecialchars($cliente['cliente_nome'] ?? $_SESSION['usuario_nome'] ?? 'Cliente'); ?>!</h5>
        <p class="text-muted mb-0">Gerencie seus agendamentos, faça novas marcações e acompanhe seu histórico.</p>
    </div>
    <a href="<?php echo base_url('cliente/agendamentos/novo'); ?>" class="btn btn-primary rounded-pill px-4 shadow-sm">
        <i class="bi bi-calendar-plus me-1"></i> Novo agendamento
    </a>
</div>

<div class="row g-4">
    <div class="col-12 col-xl-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-semibold mb-0">Próximos agendamentos</h5>
                    <small class="text-muted">Alterações de data disponíveis até 12 horas antes.</small>
                </div>
            </div>
            <div class="card-body p-0">
                <?php
                    $mapBadge = [
                        'aguardando'   => 'bg-warning text-dark',
                        'confirmado'   => 'bg-success',
                        'em_andamento' => 'bg-info text-dark',
                        'concluido'    => 'bg-primary',
                        'cancelado'    => 'bg-danger',
                    ];
                    $mapLabel = [
                        'aguardando'   => 'Aguardando',
                        'confirmado'   => 'Confirmado',
                        'em_andamento' => 'Em andamento',
                        'concluido'    => 'Concluído',
                        'cancelado'    => 'Cancelado',
                    ];
                ?>
                <?php if (empty($proximos)): ?>
                    <div class="p-4 text-center text-muted">
                        Você ainda não possui compromissos futuros. Faça uma nova reserva!
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($proximos as $ag): ?>
                            <?php
                                $status = $ag['agendamento_status'] ?? 'aguardando';
                                $badge  = $mapBadge[$status] ?? 'bg-secondary';
                                $label  = $mapLabel[$status] ?? ucfirst($status);
                                $hora   = substr($ag['agendamento_hora'] ?? '', 0, 5);
                                $data   = formatarData($ag['agendamento_data']);
                            ?>
                            <div class="list-group-item p-4">
                                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle text-center me-3 fw-bold"
                                             style="width: 52px; height: 52px; line-height: 52px;">
                                            <?php echo htmlspecialchars(mb_strtoupper(mb_substr($ag['servico_nome'] ?? '', 0, 1))); ?>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-semibold text-dark">
                                                <?php echo htmlspecialchars($ag['servico_nome'] ?? ''); ?>
                                            </h6>
                                            <div class="text-muted small">
                                                <i class="bi bi-calendar-event me-1"></i>
                                                <?php echo $data . ' às ' . htmlspecialchars($hora); ?>
                                            </div>
                                            <div class="text-muted small">
                                                <i class="bi bi-person-badge me-1"></i>
                                                Profissional: <?php echo htmlspecialchars($ag['profissional_nome'] ?? ''); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-2 ms-lg-auto">
                                        <span class="badge <?php echo $badge; ?> rounded-pill px-3 py-2 text-capitalize text-center">
                                            <?php echo $label; ?>
                                        </span>
                                        <div class="d-flex gap-2">
                                            <a href="<?php echo base_url('cliente/agendamentos/' . $ag['agendamento_id'] . '/editar'); ?>"
                                               class="btn btn-outline-secondary btn-sm rounded-pill">
                                                <i class="bi bi-pencil-square me-1"></i> Editar
                                            </a>
                                            <form action="<?php echo base_url('cliente/agendamentos/' . $ag['agendamento_id'] . '/cancelar'); ?>" method="POST"
                                                  onsubmit="return confirm('Deseja realmente cancelar este agendamento?');">
                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill">
                                                    <i class="bi bi-x-circle me-1"></i> Cancelar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white">
                <h5 class="fw-semibold mb-0">Histórico de agendamentos</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Data</th>
                                <th>Serviço</th>
                                <th>Profissional</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($historico)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        Seus agendamentos anteriores aparecerão aqui.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($historico as $ag): ?>
                                    <?php
                                        $status = $ag['agendamento_status'] ?? 'aguardando';
                                        $badge  = $mapBadge[$status] ?? 'bg-secondary';
                                        $label  = $mapLabel[$status] ?? ucfirst($status);
                                    ?>
                                    <tr>
                                        <td class="ps-4 text-nowrap">
                                            <?php echo formatarData($ag['agendamento_data']); ?>
                                            <small class="text-muted d-block">às <?php echo substr($ag['agendamento_hora'] ?? '', 0, 5); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($ag['servico_nome'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($ag['profissional_nome'] ?? ''); ?></td>
                                        <td class="text-center">
                                            <span class="badge <?php echo $badge; ?> rounded-pill px-3">
                                                <?php echo $label; ?>
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
    </div>
</div>
