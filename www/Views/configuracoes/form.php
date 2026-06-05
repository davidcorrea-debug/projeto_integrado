<?php if (!empty($msg)) echo $msg; ?>

<section class="config-wrapper py-4 py-lg-5">
    <div class="config-container">
        <div class="config-header">
            <div class="config-header-icon">
                <i class="bi bi-person-gear"></i>
            </div>
            <div>
                <span class="config-header-label text-uppercase">Conta</span>
                <h2 class="config-title fw-semibold mb-1">Configurações da conta</h2>
                <p class="config-subtitle mb-0">Atualize com simplicidade seus dados pessoais e de acesso.</p>
            </div>
        </div>

        <?php if (!empty($erros)): ?>
            <div class="alert alert-danger config-alert d-flex align-items-start gap-3 mb-4">
                <i class="bi bi-exclamation-octagon-fill fs-4"></i>
                <div>
                    <h6 class="fw-semibold mb-1">Não foi possível salvar as alterações</h6>
                    <ul class="mb-0 small ps-3">
                        <?php foreach ($erros as $erro): ?>
                            <li><?php echo htmlspecialchars($erro); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <form action="<?php echo base_url('configuracoes/salvar'); ?>" method="POST" class="config-form needs-validation" novalidate>
            <div class="form-floating mb-3">
                <input type="text" name="usuario_nome" id="configNome" class="form-control" placeholder="Nome completo" required
                       value="<?php echo htmlspecialchars($dados['usuario_nome'] ?? ''); ?>">
                <label for="configNome" class="fw-medium">Nome completo <span class="text-danger">*</span></label>
            </div>

            <div class="form-floating mb-3">
                <input type="email" name="usuario_email" id="configEmail" class="form-control" placeholder="email@exemplo.com" required
                       value="<?php echo htmlspecialchars($dados['usuario_email'] ?? ''); ?>">
                <label for="configEmail" class="fw-medium">E-mail <span class="text-danger">*</span></label>
            </div>
            <p class="text-muted small mb-4">O e-mail é utilizado para acesso à plataforma, notificações e recuperação de senha.</p>

            <?php if (($perfil ?? '') === 'cliente'): ?>
                <div class="form-floating mb-4">
                    <input type="tel" name="cliente_telefone" id="configTelefone" class="form-control" placeholder="(00) 00000-0000"
                           value="<?php echo htmlspecialchars($dados['cliente_telefone'] ?? ''); ?>">
                    <label for="configTelefone" class="fw-medium">Telefone</label>
                </div>
            <?php endif; ?>

            <div class="config-section">
                <h5 class="fw-semibold mb-3">Alterar senha</h5>
                <div class="alert alert-warning config-tip mb-4" role="alert">
                    <i class="bi bi-lightning-charge-fill me-2"></i>
                    Recomendamos uma senha forte com no mínimo 6 caracteres, combinando letras maiúsculas, minúsculas, números e símbolos.
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <div class="form-floating">
                            <input type="password" name="senha_atual" id="configSenhaAtual" class="form-control" placeholder="Senha atual">
                            <label for="configSenhaAtual">Senha atual</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating">
                            <input type="password" name="nova_senha" id="configNovaSenha" class="form-control" placeholder="Nova senha">
                            <label for="configNovaSenha">Nova senha</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating">
                            <input type="password" name="confirmar_senha" id="configConfirmarSenha" class="form-control" placeholder="Confirmar senha">
                            <label for="configConfirmarSenha">Confirmar nova senha</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-end gap-2 gap-md-3 mt-4">
                <a href="<?php echo base_url('dashboard'); ?>" class="btn btn-outline-secondary btn-lg px-4">Cancelar</a>
                <button type="submit" class="btn btn-gradient btn-lg px-4">
                    <i class="bi bi-stars me-2"></i>Salvar alterações
                </button>
            </div>
        </form>
    </div>
</section>
