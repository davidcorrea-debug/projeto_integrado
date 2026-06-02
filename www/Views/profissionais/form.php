<?php if (!empty($msg)) echo $msg; ?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-semibold"><?php echo htmlspecialchars($pagina); ?></h5>
            </div>
            <div class="card-body p-4">
                <form action="<?php echo base_url('profissionais/salvar'); ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nome completo <span class="text-danger">*</span></label>
                        <input type="text" name="usuario_nome" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">E-mail <span class="text-danger">*</span></label>
                        <input type="email" name="usuario_email" class="form-control" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Senha <span class="text-danger">*</span></label>
                            <input type="password" name="usuario_senha" class="form-control" minlength="6" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Confirmar senha <span class="text-danger">*</span></label>
                            <input type="password" name="confirmar_senha" class="form-control" minlength="6" required>
                        </div>
                    </div>

                    <div class="alert alert-info small">
                        O perfil será definido automaticamente como <strong>profissional</strong>.
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="robot bi-check-lg me-1"></i> Cadastrar
                        </button>
                        <a href="<?php echo base_url('profissionais'); ?>" class="btn btn-outline-secondary rounded-pill px-4">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
