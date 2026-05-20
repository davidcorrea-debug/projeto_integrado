<?php if (!empty($msg)) echo $msg; ?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-semibold"><?php echo htmlspecialchars($pagina); ?></h5>
            </div>
            <div class="card-body p-4">
                <?php
                    $action = !empty($cliente['cliente_id'])
                        ? base_url('clientes/atualizar/' . $cliente['cliente_id'])
                        : base_url('clientes/salvar');
                ?>
                <form action="<?php echo $action; ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nome completo <span class="text-danger">*</span></label>
                        <input type="text" name="cliente_nome" class="form-control"
                               value="<?php echo htmlspecialchars($cliente['cliente_nome'] ?? ''); ?>" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">E-mail</label>
                            <input type="email" name="cliente_email" class="form-control"
                                   value="<?php echo htmlspecialchars($cliente['cliente_email'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Telefone / WhatsApp</label>
                            <input type="text" name="cliente_telefone" class="form-control"
                                   placeholder="(00) 00000-0000"
                                   value="<?php echo htmlspecialchars($cliente['cliente_telefone'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Data de Nascimento</label>
                        <input type="date" name="cliente_nascimento" class="form-control"
                               value="<?php echo $cliente['cliente_nascimento'] ?? ''; ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-medium">Observações</label>
                        <textarea name="cliente_obs" class="form-control" rows="3"
                                  placeholder="Alergias, preferências, etc."><?php echo htmlspecialchars($cliente['cliente_obs'] ?? ''); ?></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-check-lg me-1"></i>
                            <?php echo !empty($cliente['cliente_id']) ? 'Atualizar' : 'Cadastrar'; ?>
                        </button>
                        <a href="<?php echo base_url('clientes'); ?>" class="btn btn-outline-secondary rounded-pill px-4">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
