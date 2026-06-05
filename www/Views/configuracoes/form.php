<?php if (!empty($msg)) echo $msg; ?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8 col-xl-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pb-0">
                <h5 class="fw-semibold mb-1">Configurações da conta</h5>
                <p class="text-muted mb-0">Atualize seus dados de acesso com segurança.</p>
            </div>
            <div class="card-body">
                <?php if (!empty($erros)): ?>
                    <div class="alert alert-danger">
                        <strong>Não foi possível salvar:</strong>
                        <ul class="mb-0 small">
                            <?php foreach ($erros as $erro): ?>
                                <li><?php echo htmlspecialchars($erro); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?php echo base_url('configuracoes/salvar'); ?>" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nome completo <span class="text-danger">*</span></label>
                        <input type="text" name="usuario_nome" class="form-control" required
                               value="<?php echo htmlspecialchars($dados['usuario_nome'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">E-mail <span class="text-danger">*</span></label>
                        <input type="email" name="usuario_email" class="form-control" required
                               value="<?php echo htmlspecialchars($dados['usuario_email'] ?? ''); ?>">
                        <small class="text-muted">O e-mail também é usado para login.</small>
                    </div>

                    <?php if (($perfil ?? '') === 'cliente'): ?>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Telefone</label>
                            <input type="tel" name="cliente_telefone" class="form-control"
                                   value="<?php echo htmlspecialchars($dados['cliente_telefone'] ?? ''); ?>"
                                   placeholder="(00) 00000-0000">
                        </div>
                    <?php endif; ?>

                    <hr class="my-4">
                    <h6 class="fw-semibold">Alterar senha</h6>
                    <p class="text-muted small">Preencha os campos abaixo somente se desejar trocar sua senha.</p>

                    <div class="mb-3">
                        <label class="form-label">Senha atual</label>
                        <input type="password" name="senha_atual" class="form-control" placeholder="Digite sua senha atual">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nova senha</label>
                            <input type="password" name="nova_senha" class="form-control" placeholder="Mínimo 6 caracteres">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirmar nova senha</label>
                            <input type="password" name="confirmar_senha" class="form-control" placeholder="Repita a nova senha">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="<?php echo base_url('dashboard'); ?>" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Salvar alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
