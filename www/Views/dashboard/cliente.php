<?php if (!empty($_SESSION['msg'])) echo $_SESSION['msg']; ?>

<?php
    $clienteNome   = htmlspecialchars($cliente['cliente_nome'] ?? ($_SESSION['usuario_nome'] ?? 'Cliente'));
    $totalProximos = $totalProximos ?? 0;
    $proximaSessao = $proximoPrincipal ?? null;
?>

<section class="client-dashboard py-4 py-lg-5">
    <div class="row g-4 mb-4 justify-content-end">
        <div class="col-12 col-lg-10 col-xl-8 col-xxl-7 d-flex flex-column gap-4">
            <div class="client-card client-dashboard__intro">
                <div class="client-dashboard__greeting text-lg-end">
                    <span class="badge rounded-pill text-bg-light text-uppercase fw-semibold small mb-2">Bem-vindo(a)</span>
                    <h2 class="fw-semibold mb-1">Olá, <?php echo $clienteNome; ?>!</h2>
                    <p class="text-muted mb-0">Aqui está um resumo rápido da sua agenda e atalhos para facilitar o próximo atendimento.</p>
                </div>
                <div class="client-dashboard__actions">
                    <a href="<?php echo base_url('cliente/agendamentos/novo'); ?>" class="btn btn-gradient btn-lg px-4">
                        <i class="bi bi-calendar-plus me-2"></i> Novo agendamento
                    </a>
                    <a href="<?php echo base_url('cliente/agendamentos'); ?>" class="btn btn-outline-secondary btn-lg px-4">
                        <i class="bi bi-list-check me-2"></i> Ver meus agendamentos
                    </a>
                </div>
            </div>

            <div class="client-card flex-grow-1">
                <div class="client-card__header d-flex align-items-start align-items-lg-center gap-3 mb-3">
                    <div class="text-lg-end">
                        <h5 class="fw-semibold mb-1">Próximo atendimento</h5>
                        <span class="text-muted small">Resumo do compromisso mais recente agendado</span>
                    </div>
                    <?php if ($proximaSessao): ?>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 ms-lg-auto">
                            <?php echo formatarData($proximaSessao['agendamento_data']); ?> às <?php echo substr($proximaSessao['agendamento_hora'] ?? '', 0, 5); ?>
                        </span>
                    <?php endif; ?>
                </div>

                <?php if ($proximaSessao): ?>
                    <div class="client-next">
                        <div class="client-next__icon">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="fw-semibold mb-1"><?php echo htmlspecialchars($proximaSessao['servico_nome'] ?? ''); ?></h4>
                            <ul class="list-unstyled mb-0 text-muted small">
                                <li><i class="bi bi-person-badge me-2"></i>Profissional: <?php echo htmlspecialchars($proximaSessao['profissional_nome'] ?? ''); ?></li>
                                <li><i class="bi bi-geo-alt me-2"></i>Glow Agenda • Unidade principal</li>
                                <li><i class="bi bi-card-checklist me-2"></i>Status atual: <span class="text-capitalize"><?php echo htmlspecialchars($proximaSessao['agendamento_status'] ?? ''); ?></span></li>
                            </ul>
                        </div>
                        <div class="client-next__cta text-end">
                            <a href="<?php echo base_url('cliente/agendamentos/' . ($proximaSessao['agendamento_id'] ?? 0) . '/editar'); ?>" class="btn btn-outline-secondary btn-sm rounded-pill mb-2">
                                <i class="bi bi-pencil-square me-1"></i> Remarcar
                            </a>
                            <form action="<?php echo base_url('cliente/agendamentos/' . ($proximaSessao['agendamento_id'] ?? 0) . '/cancelar'); ?>" method="POST" onsubmit="return confirm('Deseja realmente cancelar este agendamento?');">
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill w-100">
                                    <i class="bi bi-x-circle me-1"></i> Cancelar
                                </button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-calendar2-week display-5 text-muted"></i>
                        <p class="mt-3 mb-1 fw-semibold">Nenhum compromisso agendado.</p>
                        <p class="text-muted">Que tal agendar um novo horário agora mesmo?</p>
                        <a href="<?php echo base_url('cliente/agendamentos/novo'); ?>" class="btn btn-gradient btn-lg mt-2">
                            <i class="bi bi-calendar-plus me-2"></i> Agendar agora
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-6">
            <div class="client-card h-100">
                <div class="client-card__header d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-semibold mb-1">Próximos compromissos</h5>
                        <span class="text-muted small">Você tem <?php echo $totalProximos; ?> agendamentos ativos.</span>
                    </div>
                    <a href="<?php echo base_url('cliente/agendamentos'); ?>" class="btn btn-sm btn-outline-primary rounded-pill">Ver agenda</a>
                </div>
                <?php if (empty($proximos)): ?>
                    <div class="text-center text-muted py-4">Sem compromissos futuros. Faça uma nova reserva!</div>
                <?php else: ?>
                    <ul class="list-group list-group-flush client-list">
                        <?php foreach ($proximos as $ag): ?>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="fw-semibold mb-1"><?php echo htmlspecialchars($ag['servico_nome'] ?? ''); ?></h6>
                                        <span class="text-muted small">
                                            <?php echo formatarData($ag['agendamento_data']); ?> às <?php echo substr($ag['agendamento_hora'] ?? '', 0, 5); ?>
                                        </span>
                                    </div>
                                    <span class="badge bg-light text-dark rounded-pill text-capitalize">
                                        <?php echo htmlspecialchars($ag['agendamento_status'] ?? ''); ?>
                                    </span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="client-card h-100">
                <div class="client-card__header d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-semibold mb-1">Histórico recente</h5>
                        <span class="text-muted small">Últimos atendimentos concluídos</span>
                    </div>
                </div>
                <?php if (empty($historico)): ?>
                    <div class="text-center text-muted py-4">Nenhum atendimento concluído recentemente.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle client-history mb-0">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Serviço</th>
                                    <th>Profissional</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historico as $ag): ?>
                                    <tr>
                                        <td><?php echo formatarData($ag['agendamento_data']); ?><br><small class="text-muted">às <?php echo substr($ag['agendamento_hora'] ?? '', 0, 5); ?></small></td>
                                        <td><?php echo htmlspecialchars($ag['servico_nome'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($ag['profissional_nome'] ?? ''); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
