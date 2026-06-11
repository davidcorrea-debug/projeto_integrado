<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div class="agenda-hero">
        <p class="agenda-hero__title mb-2">Controle a agenda e visualize os compromissos</p>
        <div class="agenda-hero__bar"></div>
    </div>
    <a href="<?php echo base_url('agendamentos/novo'); ?>" class="btn btn-primary rounded-pill px-4 shadow-sm">
        <i class="bi bi-calendar-plus me-1"></i> Novo Agendamento
    </a>
</div>

<!-- Navegação do Calendário e Filtros -->
<div class="row mb-4 align-items-center">
    <div class="col-md-4">
        <div class="btn-group shadow-sm agenda-date-group" role="group">
            <a href="<?php echo base_url('agendamentos?data=' . $dataAnterior); ?>" class="btn agenda-date-btn"><i class="bi bi-chevron-left"></i></a>
            <span class="btn agenda-date-display fw-semibold px-4"><?php echo formatarData($data); ?></span>
            <a href="<?php echo base_url('agendamentos?data=' . $dataProxima); ?>" class="btn agenda-date-btn"><i class="bi bi-chevron-right"></i></a>
        </div>
    </div>
    <div class="col-md-8 d-flex justify-content-md-end mt-3 mt-md-0 flex-wrap gap-2 gap-md-3">
        <div class="btn-group me-3 shadow-sm agenda-toggle-group" role="group">
            <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked>
            <label class="btn agenda-toggle" for="btnradio1">Lista</label>

            <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
            <label class="btn agenda-toggle" for="btnradio2">Dia</label>

            <input type="radio" class="btn-check" name="btnradio" id="btnradio3" autocomplete="off">
            <label class="btn agenda-toggle" for="btnradio3">Semana</label>
        </div>
        <?php if (($role ?? '') === 'admin'): ?>
            <select class="form-select agenda-select w-auto shadow-sm">
                <option selected>Todos os Profissionais</option>
            </select>
        <?php endif; ?>
    </div>
</div>

<!-- Lista de Agendamentos do Dia -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="list-group list-group-flush rounded-3">
            <?php if (empty($agendamentos)): ?>
                <div class="list-group-item p-4 text-center text-muted">
                    Nenhum agendamento para esta data.
                </div>
            <?php else: ?>
                <?php
                    $mapBadge = [
                        'aguardando'   => 'badge-status badge-status--aguardando',
                        'confirmado'   => 'badge-status badge-status--confirmado',
                        'em_andamento' => 'badge-status badge-status--em-andamento',
                        'concluido'    => 'badge-status badge-status--concluido',
                        'cancelado'    => 'badge-status badge-status--cancelado',
                    ];

                    $statusButtons = [
                        'confirmado'   => ['label' => 'Confirmar',     'context' => 'success', 'icon' => 'bi-check-circle'],
                        'em_andamento' => ['label' => 'Em andamento', 'context' => 'info',    'icon' => 'bi-lightning-charge'],
                        'concluido'    => ['label' => 'Concluir',      'context' => 'primary', 'icon' => 'bi-flag'],
                        'cancelado'    => ['label' => 'Cancelar',      'context' => 'danger',  'icon' => 'bi-x-circle', 'confirm' => 'Deseja realmente cancelar este agendamento?'],
                    ];
                ?>
                <?php foreach ($agendamentos as $a):
                    $hora = substr($a['agendamento_hora'] ?? '', 0, 5);
                    $dur  = (int)($a['servico_duracao'] ?? 0);
                    $status = $a['agendamento_status'] ?? 'aguardando';
                    $badgeClass = $mapBadge[$status] ?? 'bg-secondary';
                    $cli = $a['cliente_nome'] ?? '';
                    $iniciais = strtoupper(substr($cli, 0, 1) . substr($cli, 1, 1));
                ?>
                    <div class="list-group-item p-4 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div class="d-flex align-items-center mb-3 mb-md-0">
                            <div class="text-center me-4" style="min-width: 60px;">
                                <h4 class="fw-bold mb-0 text-dark lh-1 text-nowrap">
                                    <span class="appointment-time"><?php echo htmlspecialchars($hora); ?></span>
                                </h4>
                                <small class="text-muted"><?php echo $dur ? formatarDuracao($dur) : ''; ?></small>
                            </div>
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle text-center me-3 fw-bold" style="width: 48px; height: 48px; line-height: 48px;">
                                <?php echo htmlspecialchars($iniciais); ?>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-semibold text-dark"><?php echo htmlspecialchars($a['cliente_nome'] ?? ''); ?></h6>
                                <div class="text-muted small mb-1"><i class="bi bi-scissors me-1"></i> <?php echo htmlspecialchars($a['servico_nome'] ?? ''); ?></div>
                                <div class="text-muted small"><i class="bi bi-person-badge me-1"></i> Profissional: <?php echo htmlspecialchars($a['profissional_nome'] ?? ''); ?></div>
                                <?php if (in_array($role ?? '', ['admin','profissional'])):
                                    $observacao = trim($a['agendamento_obs'] ?? '');
                                    if ($observacao !== ''): ?>
                                        <div class="text-muted small fst-italic mt-2">
                                            <i class="bi bi-chat-left-text me-1"></i><?php echo htmlspecialchars($observacao); ?>
                                        </div>
                                <?php endif; endif; ?>
                            </div>
                        </div>
                        <div class="d-flex flex-column align-items-md-end align-items-start gap-2 status-column">
                            <span class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst(str_replace('_',' ', $status)); ?></span>
                            <?php if (in_array($role ?? '', ['admin','profissional'])): ?>
                                <div class="d-flex flex-wrap gap-2 justify-content-start justify-content-md-end status-action-group">
                                    <?php foreach ($statusButtons as $code => $config):
                                        $isCurrent = $status === $code;
                                        $context   = $config['context'];
                                        $btnClass  = $isCurrent ? 'btn-' . $context : 'btn-outline-' . $context;
                                    ?>
                                        <form action="<?php echo base_url('agendamentos/status/' . $a['agendamento_id']); ?>" method="POST" class="d-inline">
                                            <input type="hidden" name="status" value="<?php echo $code; ?>">
                                            <input type="hidden" name="data" value="<?php echo htmlspecialchars($data); ?>">
                                            <button type="submit"
                                                    class="btn btn-sm <?php echo $btnClass; ?> rounded-pill status-action-btn"
                                                    <?php echo $isCurrent ? 'disabled' : ''; ?>
                                                    <?php echo (!empty($config['confirm']) && !$isCurrent) ? "onclick=\"return confirm('{$config['confirm']}')\"" : ''; ?>>
                                                <i class="bi <?php echo $config['icon']; ?> me-1"></i>
                                                <?php echo $config['label']; ?>
                                            </button>
                                        </form>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
