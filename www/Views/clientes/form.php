<?php if (!empty($msg))
    echo $msg; ?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <!-- Header do Formulário -->
        <div class="mb-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="bg-primary rounded-circle p-3"
                    style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-person-plus text-white" style="font-size: 1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark"><?php echo htmlspecialchars($pagina); ?></h3>
                    <p class="text-muted mb-0 small">Gerencie os dados dos clientes e mantenha suas informações
                        atualizadas</p>
                </div>
            </div>
        </div>

        <!-- Card Principal -->
        <div class="card shadow-lg border-0" style="border-radius: 1.5rem; overflow: hidden;">
            <div class="card-header"
                style="background: linear-gradient(135deg, #d63384 0%, #b02a6c 100%); border: none; padding: 2rem;">
                <h5 class="mb-0 fw-semibold text-white">
                    <i class="bi bi-pencil-square me-2"></i>
                    <?php echo !empty($cliente['cliente_id']) ? 'Atualizar Cliente' : 'Novo Cliente'; ?>
                </h5>
            </div>
            <div class="card-body p-5">
                <?php
                $action = !empty($cliente['cliente_id'])
                    ? base_url('clientes/atualizar/' . $cliente['cliente_id'])
                    : base_url('clientes/salvar');
                ?>
                <form action="<?php echo $action; ?>" method="POST">
                    <!-- Informações Pessoais -->
                    <div class="mb-4">
                        <h6 class="text-muted fw-bold mb-3" style="text-transform: uppercase; font-size: 0.85rem;">
                            <i class="bi bi-info-circle me-2" style="color: #d63384;"></i> Informações Pessoais
                        </h6>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="color: #333;">
                                <i class="bi bi-person me-2" style="color: #d63384;"></i>Nome completo <span
                                    class="text-danger">*</span>
                            </label>
                            <input type="text" name="cliente_nome" class="form-control form-control-lg border-2"
                                style="border-color: #e0e0e0; border-radius: 0.75rem; transition: all 0.3s ease;"
                                placeholder="Digite o nome completo"
                                value="<?php echo htmlspecialchars($cliente['cliente_nome'] ?? ''); ?>" required
                                onfocus="this.style.borderColor='#d63384'; this.style.boxShadow='0 0 0 3px rgba(214, 51, 132, 0.1)';"
                                onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';">
                        </div>
                    </div>

                    <!-- Contato -->
                    <div class="mb-4">
                        <h6 class="text-muted fw-bold mb-3" style="text-transform: uppercase; font-size: 0.85rem;">
                            <i class="bi bi-telephone me-2" style="color: #d63384;"></i> Contato
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="color: #333;">
                                    <i class="bi bi-envelope me-2" style="color: #d63384;"></i>E-mail
                                </label>
                                <input type="email" name="cliente_email" class="form-control form-control-lg border-2"
                                    style="border-color: #e0e0e0; border-radius: 0.75rem; transition: all 0.3s ease;"
                                    placeholder="exemplo@email.com"
                                    value="<?php echo htmlspecialchars($cliente['cliente_email'] ?? ''); ?>"
                                    onfocus="this.style.borderColor='#d63384'; this.style.boxShadow='0 0 0 3px rgba(214, 51, 132, 0.1)';"
                                    onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="color: #333;">
                                    <i class="bi bi-whatsapp me-2" style="color: #d63384;"></i>Telefone / WhatsApp
                                </label>
                                <input type="text" name="cliente_telefone" class="form-control form-control-lg border-2"
                                    style="border-color: #e0e0e0; border-radius: 0.75rem; transition: all 0.3s ease;"
                                    placeholder="(00) 00000-0000"
                                    value="<?php echo htmlspecialchars($cliente['cliente_telefone'] ?? ''); ?>"
                                    onfocus="this.style.borderColor='#d63384'; this.style.boxShadow='0 0 0 3px rgba(214, 51, 132, 0.1)';"
                                    onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';">
                            </div>
                        </div>
                    </div>

                    <!-- Informações Adicionais -->
                    <div class="mb-4">
                        <h6 class="text-muted fw-bold mb-3" style="text-transform: uppercase; font-size: 0.85rem;">
                            <i class="bi bi-calendar me-2" style="color: #d63384;"></i> Informações Adicionais
                        </h6>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="color: #333;">
                                <i class="bi bi-calendar-event me-2" style="color: #d63384;"></i>Data de Nascimento
                            </label>
                            <input type="date" name="cliente_nascimento" class="form-control form-control-lg border-2"
                                style="border-color: #e0e0e0; border-radius: 0.75rem; transition: all 0.3s ease;"
                                value="<?php echo $cliente['cliente_nascimento'] ?? ''; ?>"
                                onfocus="this.style.borderColor='#d63384'; this.style.boxShadow='0 0 0 3px rgba(214, 51, 132, 0.1)';"
                                onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';">
                        </div>
                        <div>
                            <label class="form-label fw-semibold" style="color: #333;">
                                <i class="bi bi-chat-left-text me-2" style="color: #d63384;"></i>Observações
                            </label>
                            <textarea name="cliente_obs" class="form-control form-control-lg border-2" rows="4"
                                style="border-color: #e0e0e0; border-radius: 0.75rem; transition: all 0.3s ease;"
                                placeholder="Alergias, preferências, restrições, etc."
                                onfocus="this.style.borderColor='#d63384'; this.style.boxShadow='0 0 0 3px rgba(214, 51, 132, 0.1)';"
                                onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';"><?php echo htmlspecialchars($cliente['cliente_obs'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5 pt-3 border-top">
                        <a href="<?php echo base_url('clientes'); ?>"
                            class="btn btn-outline-secondary btn-lg rounded-pill px-5">
                            <i class="bi bi-x-circle me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5"
                            style="background: linear-gradient(135deg, #d63384 0%, #b02a6c 100%); border: none;">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php echo !empty($cliente['cliente_id']) ? 'Atualizar Cliente' : 'Cadastrar Cliente'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>