<?php if (!empty($msg))
    echo $msg; ?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-9">
        <!-- Header do Formulário -->
        <div class="mb-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="bg-primary rounded-circle p-3"
                    style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-calendar-plus text-white" style="font-size: 1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark">Novo Agendamento</h3>
                    <p class="text-muted mb-0 small">Agende um novo serviço para o cliente e organize sua agenda</p>
                </div>
            </div>
        </div>

        <!-- Card Principal -->
        <div class="card shadow-lg border-0" style="border-radius: 1.5rem; overflow: hidden;">
            <div class="card-header"
                style="background: linear-gradient(135deg, #d63384 0%, #b02a6c 100%); border: none; padding: 2rem;">
                <h5 class="mb-0 fw-semibold text-white">
                    <i class="bi bi-calendar-check me-2"></i> Formulário de Agendamento
                </h5>
            </div>
            <div class="card-body p-5">
                <form action="<?php echo base_url('agendamentos/salvar'); ?>" method="POST">

                    <!-- Seção: Cliente -->
                    <div class="mb-5">
                        <h6 class="text-muted fw-bold mb-3" style="text-transform: uppercase; font-size: 0.85rem;">
                            <i class="bi bi-person-circle me-2" style="color: #d63384;"></i> Dados do Cliente
                        </h6>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="color: #333;">
                                <i class="bi bi-person me-2" style="color: #d63384;"></i>Cliente <span
                                    class="text-danger">*</span>
                            </label>
                            <select name="cliente_id" class="form-select form-select-lg border-2"
                                style="border-color: #e0e0e0; border-radius: 0.75rem; transition: all 0.3s ease;"
                                required
                                onfocus="this.style.borderColor='#d63384'; this.style.boxShadow='0 0 0 3px rgba(214, 51, 132, 0.1)';"
                                onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';">
                                <option value="">-- Selecione o cliente --</option>
                                <?php foreach ($clientes as $c): ?>
                                    <option value="<?php echo $c['cliente_id']; ?>">
                                        👤 <?php echo htmlspecialchars($c['cliente_nome']); ?>
                                        <?php echo !empty($c['cliente_telefone']) ? ' (' . $c['cliente_telefone'] . ')' : ''; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="mt-2">
                                <a href="<?php echo base_url('clientes/novo'); ?>" target="_blank"
                                    class="small text-primary fw-semibold">
                                    <i class="bi bi-plus-circle me-1"></i> Cadastrar novo cliente
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Seção: Serviço -->
                    <div class="mb-5">
                        <h6 class="text-muted fw-bold mb-3" style="text-transform: uppercase; font-size: 0.85rem;">
                            <i class="bi bi-scissors me-2" style="color: #d63384;"></i> Serviço
                        </h6>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="color: #333;">
                                <i class="bi bi-star me-2" style="color: #d63384;"></i>Serviço <span
                                    class="text-danger">*</span>
                            </label>
                            <select name="servico_id" class="form-select form-select-lg border-2" id="select_servico"
                                required
                                style="border-color: #e0e0e0; border-radius: 0.75rem; transition: all 0.3s ease;"
                                onfocus="this.style.borderColor='#d63384'; this.style.boxShadow='0 0 0 3px rgba(214, 51, 132, 0.1)';"
                                onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';">
                                <option value="">-- Selecione o serviço --</option>
                                <?php foreach ($servicos as $s): ?>
                                    <option value="<?php echo $s['servico_id']; ?>"
                                        data-preco="<?php echo number_format($s['servico_preco'], 2, ',', '.'); ?>"
                                        data-duracao="<?php echo formatarDuracao($s['servico_duracao']); ?>">
                                        ✨ <?php echo htmlspecialchars($s['servico_nome']); ?>
                                        — <?php echo formatarDinheiro($s['servico_preco']); ?>
                                        (<?php echo formatarDuracao($s['servico_duracao']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Info Card de Serviço -->
                        <div class="alert alert-light border-2 border-primary rounded-3 mb-0" id="info-servico"
                            style="display: none;">
                            <div class="row align-items-center g-3">
                                <div class="col-auto">
                                    <div class="bg-primary rounded-circle p-2"
                                        style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-info-circle text-white" style="font-size: 1.2rem;"></i>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <small class="text-muted">Preço:</small>
                                            <p class="mb-0 fw-semibold text-primary" id="info-preco">R$ 0,00</p>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">Duração:</small>
                                            <p class="mb-0 fw-semibold text-primary" id="info-duracao">0 min</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção: Profissional -->
                    <div class="mb-5">
                        <h6 class="text-muted fw-bold mb-3" style="text-transform: uppercase; font-size: 0.85rem;">
                            <i class="bi bi-briefcase me-2" style="color: #d63384;"></i> Profissional
                        </h6>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="color: #333;">
                                <i class="bi bi-person-badge me-2" style="color: #d63384;"></i>Profissional <span
                                    class="text-danger">*</span>
                            </label>
                            <select name="usuario_id" class="form-select form-select-lg border-2" required
                                style="border-color: #e0e0e0; border-radius: 0.75rem; transition: all 0.3s ease;"
                                onfocus="this.style.borderColor='#d63384'; this.style.boxShadow='0 0 0 3px rgba(214, 51, 132, 0.1)';"
                                onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';">
                                <option value="">-- Selecione o profissional --</option>
                                <?php foreach ($usuarios as $u): ?>
                                    <option value="<?php echo $u['usuario_id']; ?>">
                                        👩‍⚕️ <?php echo htmlspecialchars($u['usuario_nome']); ?>
                                        <small>(<?php echo ucfirst($u['usuario_perfil']); ?>)</small>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Seção: Data e Hora -->
                    <div class="mb-5">
                        <h6 class="text-muted fw-bold mb-3" style="text-transform: uppercase; font-size: 0.85rem;">
                            <i class="bi bi-calendar-event me-2" style="color: #d63384;"></i> Agendamento
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="color: #333;">
                                    <i class="bi bi-calendar me-2" style="color: #d63384;"></i>Data <span
                                        class="text-danger">*</span>
                                </label>
                                <input type="date" name="agendamento_data" class="form-control form-control-lg border-2"
                                    style="border-color: #e0e0e0; border-radius: 0.75rem; transition: all 0.3s ease;"
                                    value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>" required
                                    onfocus="this.style.borderColor='#d63384'; this.style.boxShadow='0 0 0 3px rgba(214, 51, 132, 0.1)';"
                                    onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="color: #333;">
                                    <i class="bi bi-clock me-2" style="color: #d63384;"></i>Horário <span
                                        class="text-danger">*</span>
                                </label>
                                <input type="time" name="agendamento_hora" class="form-control form-control-lg border-2"
                                    style="border-color: #e0e0e0; border-radius: 0.75rem; transition: all 0.3s ease;"
                                    value="08:00" required
                                    onfocus="this.style.borderColor='#d63384'; this.style.boxShadow='0 0 0 3px rgba(214, 51, 132, 0.1)';"
                                    onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';">
                            </div>
                        </div>
                    </div>

                    <!-- Seção: Observações -->
                    <div class="mb-5">
                        <h6 class="text-muted fw-bold mb-3" style="text-transform: uppercase; font-size: 0.85rem;">
                            <i class="bi bi-chat-left-text me-2" style="color: #d63384;"></i> Observações
                        </h6>
                        <div>
                            <label class="form-label fw-semibold" style="color: #333;">
                                <i class="bi bi-chat-left me-2" style="color: #d63384;"></i>Informações Adicionais
                            </label>
                            <textarea name="agendamento_obs" class="form-control form-control-lg border-2" rows="3"
                                style="border-color: #e0e0e0; border-radius: 0.75rem; transition: all 0.3s ease;"
                                placeholder="Digite qualquer informação adicional relevante..."
                                onfocus="this.style.borderColor='#d63384'; this.style.boxShadow='0 0 0 3px rgba(214, 51, 132, 0.1)';"
                                onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';"></textarea>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5 pt-3 border-top">
                        <a href="<?php echo base_url('agendamentos'); ?>"
                            class="btn btn-outline-secondary btn-lg rounded-pill px-5">
                            <i class="bi bi-x-circle me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5"
                            style="background: linear-gradient(135deg, #d63384 0%, #b02a6c 100%); border: none;">
                            <i class="bi bi-check-circle me-2"></i>Confirmar Agendamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script para atualizar info do serviço -->
<script>
    document.getElementById('select_servico').addEventListener('change', function () {
        const option = this.options[this.selectedIndex];
        const infoDiv = document.getElementById('info-servico');

        if (this.value) {
            document.getElementById('info-preco').textContent = 'R$ ' + option.getAttribute('data-preco');
            document.getElementById('info-duracao').textContent = option.getAttribute('data-duracao');
            infoDiv.style.display = 'block';
        } else {
            infoDiv.style.display = 'none';
        }
    });
</script>