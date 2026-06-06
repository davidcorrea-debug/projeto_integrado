<?php if (!empty($msg)) echo $msg; ?>

<?php
    $totalProximos   = is_countable($proximos ?? []) ? count($proximos) : 0;
    $totalHistorico  = is_countable($historico ?? []) ? count($historico) : 0;
    $primeiroProximo = $totalProximos > 0 ? $proximos[0] : null;
    $proximoResumo   = $primeiroProximo
        ? formatarData($primeiroProximo['agendamento_data']) . ' às ' . substr($primeiroProximo['agendamento_hora'] ?? '', 0, 5)
        : 'Nenhum compromisso futuro';

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

<section class="schedule-wrapper py-4 py-lg-5">
    <div class="schedule-hero mb-4">
        <div class="row align-items-center g-4">
            <div class="col">
                <h2 class="schedule-title fw-semibold mb-1">Meus agendamentos</h2>
                <p class="schedule-subtitle mb-0">Gerencie suas próximas visitas e confira o histórico completo.</p>
            </div>
            <div class="col-12 col-lg-auto">
                <a href="<?php echo base_url('cliente/agendamentos/novo'); ?>" class="btn btn-gradient btn-lg px-4 w-100 w-lg-auto">
                    <i class="bi bi-calendar-plus me-2"></i>
                    Novo agendamento
                </a>
            </div>
        </div>

        <div class="schedule-summary mt-4">
            <div class="schedule-summary-card">
                <span class="schedule-summary-label">Próximos</span>
                <strong class="schedule-summary-value" id="summary-total-proximos"><?php echo $totalProximos; ?></strong>
                <small class="schedule-summary-hint">Reservas ativas</small>
            </div>
            <div class="schedule-summary-card">
                <span class="schedule-summary-label">Próximo horário</span>
                <strong class="schedule-summary-value fs-6" id="summary-proximo-horario"><?php echo htmlspecialchars($proximoResumo); ?></strong>
                <small class="schedule-summary-hint">Atualizado periodicamente</small>
            </div>
            <div class="schedule-summary-card">
                <span class="schedule-summary-label">Histórico</span>
                <strong class="schedule-summary-value" id="summary-total-historico"><?php echo $totalHistorico; ?></strong>
                <small class="schedule-summary-hint">Atendimentos concluídos</small>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-6">
            <div class="schedule-card h-100">
                <div class="schedule-card-header">
                    <div>
                        <h5 class="mb-1 fw-semibold">Próximos agendamentos</h5>
                        <span class="text-muted small">Remarcações liberadas até 12h antes.</span>
                    </div>
                </div>
                <div class="schedule-card-body">
                    <div class="schedule-empty text-center <?php echo empty($proximos) ? '' : 'd-none'; ?>" id="appointment-empty">
                        <i class="bi bi-calendar2-week"></i>
                        <p class="mb-1 fw-semibold">Sem compromissos por aqui.</p>
                        <span class="text-muted">Faça uma nova reserva para manter sua agenda em dia.</span>
                    </div>
                    <div class="appointment-list <?php echo empty($proximos) ? 'd-none' : ''; ?>" id="appointment-list">
                        <?php foreach ($proximos as $ag): ?>
                            <?php
                                $status = $ag['agendamento_status'] ?? 'aguardando';
                                $badge  = $mapBadge[$status] ?? 'bg-secondary';
                                $label  = $mapLabel[$status] ?? ucfirst($status);
                                $hora   = substr($ag['agendamento_hora'] ?? '', 0, 5);
                                $data   = formatarData($ag['agendamento_data']);
                            ?>
                            <article class="appointment-item">
                                <div class="appointment-item__meta">
                                    <span class="appointment-date">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        <?php echo $data; ?>
                                    </span>
                                    <span class="appointment-time">
                                        <i class="bi bi-clock me-1"></i>
                                        <?php echo htmlspecialchars($hora); ?>
                                    </span>
                                </div>
                                <div class="appointment-item__content">
                                    <div class="appointment-item__icon">
                                        <span><?php echo htmlspecialchars(mb_strtoupper(mb_substr($ag['servico_nome'] ?? '', 0, 1))); ?></span>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1"><?php echo htmlspecialchars($ag['servico_nome'] ?? ''); ?></h6>
                                        <p class="text-muted small mb-0">
                                            <i class="bi bi-person-badge me-1"></i>
                                            Profissional: <?php echo htmlspecialchars($ag['profissional_nome'] ?? ''); ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="appointment-item__actions">
                                    <span class="badge <?php echo $badge; ?> rounded-pill px-3 py-2 text-capitalize">
                                        <?php echo $label; ?>
                                    </span>
                                    <div class="d-flex gap-2">
                                        <a href="<?php echo base_url('cliente/agendamentos/' . $ag['agendamento_id'] . '/editar'); ?>" class="btn btn-outline-secondary btn-sm rounded-pill">
                                            <i class="bi bi-pencil-square me-1"></i> Editar
                                        </a>
                                        <form action="<?php echo base_url('cliente/agendamentos/' . $ag['agendamento_id'] . '/cancelar'); ?>" method="POST" onsubmit="return confirm('Deseja realmente cancelar este agendamento?');">
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill">
                                                <i class="bi bi-x-circle me-1"></i> Cancelar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="schedule-card h-100">
                <div class="schedule-card-header">
                    <h5 class="mb-1 fw-semibold">Histórico de agendamentos</h5>
                    <span class="text-muted small">Acompanhe atendimentos anteriores e status finais.</span>
                </div>
                <div class="schedule-card-body">
                    <div class="table-responsive">
                        <table class="table schedule-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Data</th>
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
                                            <td class="text-nowrap">
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
</section>

<script>
    (function() {
        const SUMMARY_ENDPOINT = '<?php echo base_url('cliente/agendamentos/resumo'); ?>';
        const EDIT_BASE = '<?php echo base_url('cliente/agendamentos'); ?>/';
        const CANCEL_BASE = '<?php echo base_url('cliente/agendamentos'); ?>/';
        const REFRESH_INTERVAL = 60000; // 60s

        const totalProximosEl = document.getElementById('summary-total-proximos');
        const proximoHorarioEl = document.getElementById('summary-proximo-horario');
        const totalHistoricoEl = document.getElementById('summary-total-historico');
        const listEl = document.getElementById('appointment-list');
        const emptyEl = document.getElementById('appointment-empty');

        if (!totalProximosEl || !proximoHorarioEl || !totalHistoricoEl || !listEl || !emptyEl) {
            return;
        }

        const escapeHtml = (value) => {
            if (value === null || value === undefined) return '';
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        };

        const renderList = (items) => {
            if (!Array.isArray(items) || items.length === 0) {
                emptyEl.classList.remove('d-none');
                listEl.classList.add('d-none');
                listEl.innerHTML = '';
                return;
            }

            emptyEl.classList.add('d-none');
            listEl.classList.remove('d-none');

            const articles = items.map((item) => {
                const data = escapeHtml(item.data);
                const hora = escapeHtml(item.hora);
                const servico = escapeHtml(item.servico);
                const profissional = escapeHtml(item.profissional);
                const statusBadge = escapeHtml(item.statusBadge);
                const statusLabel = escapeHtml(item.statusLabel);
                const initial = escapeHtml((item.servico || '').charAt(0).toUpperCase());
                const editarHref = EDIT_BASE + item.id + '/editar';
                const cancelarHref = CANCEL_BASE + item.id + '/cancelar';

                return `
                    <article class="appointment-item">
                        <div class="appointment-item__meta">
                            <span class="appointment-date"><i class="bi bi-calendar-event me-1"></i>${data}</span>
                            <span class="appointment-time"><i class="bi bi-clock me-1"></i>${hora}</span>
                        </div>
                        <div class="appointment-item__content">
                            <div class="appointment-item__icon"><span>${initial}</span></div>
                            <div>
                                <h6 class="fw-semibold mb-1">${servico}</h6>
                                <p class="text-muted small mb-0"><i class="bi bi-person-badge me-1"></i>Profissional: ${profissional}</p>
                            </div>
                        </div>
                        <div class="appointment-item__actions">
                            <span class="badge ${statusBadge} rounded-pill px-3 py-2 text-capitalize">${statusLabel}</span>
                            <div class="d-flex gap-2">
                                <a href="${editarHref}" class="btn btn-outline-secondary btn-sm rounded-pill">
                                    <i class="bi bi-pencil-square me-1"></i> Editar
                                </a>
                                <form action="${cancelarHref}" method="POST" onsubmit="return confirm('Deseja realmente cancelar este agendamento?');">
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill">
                                        <i class="bi bi-x-circle me-1"></i> Cancelar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                `;
            });

            listEl.innerHTML = articles.join('');
        };

        const atualizarResumo = async () => {
            try {
                const response = await fetch(SUMMARY_ENDPOINT, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    cache: 'no-store'
                });

                if (!response.ok) {
                    throw new Error('Resposta inválida do servidor');
                }

                const data = await response.json();

                if (typeof data.totalProximos === 'number') {
                    totalProximosEl.textContent = data.totalProximos;
                }
                if (typeof data.totalHistorico === 'number') {
                    totalHistoricoEl.textContent = data.totalHistorico;
                }
                if (typeof data.proximoResumo === 'string') {
                    proximoHorarioEl.textContent = data.proximoResumo;
                }
                renderList(data.proximos || []);
            } catch (error) {
                console.error('[Agendamentos] Falha ao atualizar resumo:', error);
            }
        };

        const iniciarAtualizacao = () => {
            atualizarResumo();
            setInterval(atualizarResumo, REFRESH_INTERVAL);
        };

        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            iniciarAtualizacao();
        } else {
            document.addEventListener('DOMContentLoaded', iniciarAtualizacao);
        }
    })();
</script>
