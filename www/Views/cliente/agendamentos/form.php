<?php if (!empty($msg)) echo $msg; ?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-semibold"><?php echo htmlspecialchars($pagina ?? 'Agendamento'); ?></h5>
                <small class="text-muted">Lembre-se: alterações de data/hora são permitidas até 12 horas antes do compromisso.</small>
            </div>
            <div class="card-body p-4">
                <form action="<?php echo htmlspecialchars($action); ?>" method="POST" class="needs-validation" novalidate>

                    <!-- Serviço -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">Serviço <span class="text-danger">*</span></label>
                        <select name="servico_id" class="form-select" required>
                            <option value="">Selecione o serviço...</option>
                            <?php foreach ($servicos as $s): ?>
                                <option value="<?php echo $s['servico_id']; ?>"
                                        data-preco="<?php echo number_format($s['servico_preco'], 2, ',', '.'); ?>"
                                        data-duracao="<?php echo formatarDuracao($s['servico_duracao']); ?>"
                                        <?php echo ((int)($agendamento['servico_id'] ?? 0) === (int)$s['servico_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($s['servico_nome']); ?> — <?php echo formatarDinheiro($s['servico_preco']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Profissional -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">Profissional <span class="text-danger">*</span></label>
                        <select name="usuario_id" class="form-select" required>
                            <option value="">Selecione o profissional...</option>
                            <?php foreach ($profissionais as $p): ?>
                                <option value="<?php echo $p['usuario_id']; ?>"
                                        <?php echo ((int)($agendamento['usuario_id'] ?? 0) === (int)$p['usuario_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($p['usuario_nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Data e Hora -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Data <span class="text-danger">*</span></label>
                            <input type="date" name="agendamento_data" class="form-control"
                                   value="<?php echo htmlspecialchars($agendamento['agendamento_data'] ?? date('Y-m-d')); ?>"
                                   min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Horário <span class="text-danger">*</span></label>
                            <input type="time" name="agendamento_hora" class="form-control"
                                   value="<?php echo htmlspecialchars($agendamento['agendamento_hora'] ?? '08:00'); ?>" required>
                        </div>
                    </div>

                    <!-- Observações -->
                    <div class="mb-4">
                        <label class="form-label fw-medium">Observações</label>
                        <textarea name="agendamento_obs" class="form-control" rows="3"
                                  placeholder="Alguma informação importante?"><?php echo htmlspecialchars($agendamento['agendamento_obs'] ?? ''); ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-check2-circle me-1"></i> Salvar
                        </button>
                        <a href="<?php echo base_url('cliente/agendamentos'); ?>" class="btn btn-outline-secondary rounded-pill px-4">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
