<?php if (!empty($msg)) echo $msg; ?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-semibold">Novo Agendamento</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?php echo base_url('agendamentos/salvar'); ?>" method="POST">

                    <!-- Cliente -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">Cliente <span class="text-danger">*</span></label>
                        <select name="cliente_id" class="form-select" required>
                            <option value="">Selecione o cliente...</option>
                            <?php foreach ($clientes as $c): ?>
                                <option value="<?php echo $c['cliente_id']; ?>">
                                    <?php echo htmlspecialchars($c['cliente_nome']); ?>
                                    <?php echo !empty($c['cliente_telefone']) ? ' — ' . $c['cliente_telefone'] : ''; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="mt-1">
                            <a href="<?php echo base_url('clientes/novo'); ?>" target="_blank" class="small text-primary">
                                <i class="bi bi-plus-circle me-1"></i> Cadastrar novo cliente
                            </a>
                        </div>
                    </div>

                    <!-- Serviço -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">Serviço <span class="text-danger">*</span></label>
                        <select name="servico_id" class="form-select" required id="select_servico">
                            <option value="">Selecione o serviço...</option>
                            <?php foreach ($servicos as $s): ?>
                                <option value="<?php echo $s['servico_id']; ?>"
                                        data-preco="<?php echo number_format($s['servico_preco'], 2, ',', '.'); ?>"
                                        data-duracao="<?php echo formatarDuracao($s['servico_duracao']); ?>">
                                    <?php echo htmlspecialchars($s['servico_nome']); ?>
                                    — <?php echo formatarDinheiro($s['servico_preco']); ?>
                                    (<?php echo formatarDuracao($s['servico_duracao']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Profissional -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">Profissional <span class="text-danger">*</span></label>
                        <select name="usuario_id" class="form-select" required <?php echo !empty($bloqueiaSelecaoProfissional) ? 'disabled' : ''; ?>>
                            <option value="">Selecione o profissional...</option>
                            <?php foreach ($usuarios as $u): ?>
                                <option value="<?php echo $u['usuario_id']; ?>"
                                        <?php echo ($profissionalSelecionado !== null && (int)$profissionalSelecionado === (int)$u['usuario_id']) ? 'selected' : ''; ?>
                                >
                                    <?php echo htmlspecialchars($u['usuario_nome']); ?>
                                    (<?php echo ucfirst($u['usuario_perfil']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($bloqueiaSelecaoProfissional) && $profissionalSelecionado !== null): ?>
                            <input type="hidden" name="usuario_id" value="<?php echo (int)$profissionalSelecionado; ?>">
                            <small class="text-muted d-block mt-1"><i class="bi bi-person-check me-1"></i> Você está criando um agendamento como profissional logado.</small>
                        <?php endif; ?>
                    </div>

                    <!-- Data e Hora -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Data <span class="text-danger">*</span></label>
                            <input type="date" name="agendamento_data" class="form-control"
                                   value="<?php echo date('Y-m-d'); ?>"
                                   min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Horário <span class="text-danger">*</span></label>
                            <input type="time" name="agendamento_hora" class="form-control"
                                   value="08:00" required>
                        </div>
                    </div>

                    <!-- Observações -->
                    <div class="mb-4">
                        <label class="form-label fw-medium">Observações</label>
                        <textarea name="agendamento_obs" class="form-control" rows="2"
                                  placeholder="Informações adicionais..."></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-calendar-check me-1"></i> Confirmar Agendamento
                        </button>
                        <a href="<?php echo base_url('agendamentos'); ?>" class="btn btn-outline-secondary rounded-pill px-4">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
