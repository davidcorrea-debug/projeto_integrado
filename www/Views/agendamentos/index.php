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
    <div class="col-md-8 d-flex justify-content-md-end mt-3 mt-md-0">
        <div class="btn-group me-3 shadow-sm agenda-toggle-group" role="group">
            <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked>
            <label class="btn agenda-toggle" for="btnradio1">Lista</label>

            <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
            <label class="btn agenda-toggle" for="btnradio2">Dia</label>

            <input type="radio" class="btn-check" name="btnradio" id="btnradio3" autocomplete="off">
            <label class="btn agenda-toggle" for="btnradio3">Semana</label>
        </div>
        <select class="form-select agenda-select w-auto shadow-sm">
            <option selected>Todos os Profissionais</option>
        </select>
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
                        'aguardando'   => 'bg-warning text-dark',
                        'confirmado'   => 'bg-success',
                        'em_andamento' => 'bg-primary',
                        'concluido'    => 'bg-secondary',
                        'cancelado'    => 'bg-danger',
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
                                <h4 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($hora); ?></h4>
                                <small class="text-muted"><?php echo $dur ? formatarDuracao($dur) : ''; ?></small>
                            </div>
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle text-center me-3 fw-bold" style="width: 48px; height: 48px; line-height: 48px;">
                                <?php echo htmlspecialchars($iniciais); ?>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-semibold text-dark"><?php echo htmlspecialchars($a['cliente_nome'] ?? ''); ?></h6>
                                <div class="text-muted small mb-1"><i class="bi bi-scissors me-1"></i> <?php echo htmlspecialchars($a['servico_nome'] ?? ''); ?></div>
                                <div class="text-muted small"><i class="bi bi-person-badge me-1"></i> Profissional: <?php echo htmlspecialchars($a['profissional_nome'] ?? ''); ?></div>
                            </div>
                        </div>
                        <div class="d-flex flex-column align-items-md-end">
                            <span class="badge <?php echo $badgeClass; ?> rounded-pill mb-2 px-3 py-2"><?php echo ucfirst(str_replace('_',' ', $status)); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
