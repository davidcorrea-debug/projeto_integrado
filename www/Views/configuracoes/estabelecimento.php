<?php
    $dadosForm = array_merge(
        (array)($estabelecimento ?? []),
        (array)($dados ?? [])
    );

    $logoAtual = $dadosForm['logo'] ?? ($estabelecimento['logo'] ?? '');
?>

<div class="salon-dashboard">
    <header class="salon-hero">
        <div class="salon-hero__icon">
            <i class="bi bi-building"></i>
        </div>
        <div class="salon-hero__copy">
            <span class="salon-hero__eyebrow">Meu Salão</span>
            <h1 class="salon-hero__title">Gerencie as informações do seu salão</h1>
            <p class="salon-hero__subtitle">Personalize a experiência dos seus clientes na tela de login.</p>
        </div>
    </header>

    <?php if (!empty($msg)): ?>
        <div class="alert alert-success salon-alert" role="alert"><?php echo $msg; ?></div>
    <?php endif; ?>

    <?php if (!empty($erros)): ?>
        <div class="alert alert-danger salon-alert" role="alert">
            <ul class="mb-0">
                <?php foreach ($erros as $erro): ?>
                    <li><?php echo htmlspecialchars($erro); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo base_url('estabelecimento/salvar'); ?>" method="POST" enctype="multipart/form-data" class="salon-layout">
        <div class="row g-4">
            <div class="col-12 col-lg-8">
                <section class="salon-card">
                    <h2 class="salon-card__title">Dados principais</h2>
                    <p class="salon-card__subtitle">Informe o nome social, nome fantasia, CNPJ e telefone que identificam oficialmente o seu salão.</p>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Nome do salão *</label>
                            <div class="input-group salon-input">
                                <span class="input-group-text"><i class="bi bi-house-heart"></i></span>
                                <input type="text" class="form-control" name="nome" value="<?php echo htmlspecialchars($dadosForm['nome'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Nome fantasia</label>
                            <div class="input-group salon-input">
                                <span class="input-group-text"><i class="bi bi-stars"></i></span>
                                <input type="text" class="form-control" name="nome_fantasia" value="<?php echo htmlspecialchars($dadosForm['nome_fantasia'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">CNPJ *</label>
                            <div class="input-group salon-input">
                                <span class="input-group-text"><i class="bi bi-file-earmark-text"></i></span>
                                <input type="text" class="form-control" name="cnpj" value="<?php echo htmlspecialchars($dadosForm['cnpj'] ?? ''); ?>" placeholder="00.000.000/0000-00" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Telefone *</label>
                            <div class="input-group salon-input">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="text" class="form-control" name="telefone" value="<?php echo htmlspecialchars($dadosForm['telefone'] ?? ''); ?>" placeholder="(00) 00000-0000" required>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="salon-card">
                    <h2 class="salon-card__title">Contato e endereço</h2>
                    <p class="salon-card__subtitle">Atualize endereço completo, canais de contato e redes sociais para que clientes encontrem o salão com facilidade.</p>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Endereço completo *</label>
                            <div class="input-group salon-input">
                                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                <textarea class="form-control" name="endereco" rows="2" placeholder="Rua, número, bairro, cidade - UF" required><?php echo htmlspecialchars($dadosForm['endereco'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">CEP *</label>
                            <div class="input-group salon-input">
                                <span class="input-group-text"><i class="bi bi-mailbox"></i></span>
                                <input type="text" class="form-control" name="cep" value="<?php echo htmlspecialchars($dadosForm['cep'] ?? ''); ?>" placeholder="00000-000" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">E-mail</label>
                            <div class="input-group salon-input">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($dadosForm['email'] ?? ''); ?>" placeholder="contato@meusalao.com">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Link de localização (Google Maps)</label>
                            <div class="input-group salon-input">
                                <span class="input-group-text"><i class="bi bi-pin-map"></i></span>
                                <input type="url" class="form-control" name="localizacao_url" value="<?php echo htmlspecialchars($dadosForm['localizacao_url'] ?? ''); ?>" placeholder="https://maps.google.com/...">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Instagram</label>
                            <div class="input-group salon-input">
                                <span class="input-group-text"><i class="bi bi-instagram"></i></span>
                                <input type="url" class="form-control" name="instagram" value="<?php echo htmlspecialchars($dadosForm['instagram'] ?? ''); ?>" placeholder="https://instagram.com/...">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Facebook</label>
                            <div class="input-group salon-input">
                                <span class="input-group-text"><i class="bi bi-facebook"></i></span>
                                <input type="url" class="form-control" name="facebook" value="<?php echo htmlspecialchars($dadosForm['facebook'] ?? ''); ?>" placeholder="https://facebook.com/...">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Site</label>
                            <div class="input-group salon-input">
                                <span class="input-group-text"><i class="bi bi-globe"></i></span>
                                <input type="url" class="form-control" name="site" value="<?php echo htmlspecialchars($dadosForm['site'] ?? ''); ?>" placeholder="https://...">
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-12 col-lg-4">
                <aside class="salon-aside">
                    <h2 class="salon-aside__title">Personalize o visual do seu salão</h2>
                    <p class="salon-aside__hint">Adicione uma logo para deixar a tela de login com a cara da sua marca.</p>

                    <div class="salon-logo-card">
                        <span class="salon-logo-card__label">Logo atual</span>
                        <div class="salon-logo-card__preview">
                            <?php if (!empty($logoAtual)): ?>
                                <img src="<?php echo base_url($logoAtual); ?>" alt="Logo atual do salão">
                            <?php else: ?>
                                <img src="<?php echo base_url('public/assets/img/logo-unissex.svg'); ?>" alt="Logo padrão Glow Agenda">
                            <?php endif; ?>
                        </div>
                        <label class="btn btn-gradient w-100 mt-3 position-relative overflow-hidden">
                            <input type="file" class="salon-upload-input" name="logo" accept="image/jpeg,image/png,image/webp">
                            <span><i class="bi bi-upload me-2"></i>Escolher arquivo</span>
                        </label>
                        <small class="text-muted d-block mt-2">Formatos aceitos: JPG, PNG, WEBP — até 2MB</small>
                        <?php if (!empty($logoAtual)): ?>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="remover_logo" id="removerLogo">
                                <label class="form-check-label" for="removerLogo">Remover logo atual</label>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="salon-preview">
                        <span class="salon-preview__label">Prévia da tela de login</span>
                        <div class="salon-preview__mockup">
                            <div class="salon-preview__logo">
                                <?php if (!empty($logoAtual)): ?>
                                    <img src="<?php echo base_url($logoAtual); ?>" alt="Prévia logo">
                                <?php else: ?>
                                    <img src="<?php echo base_url('public/assets/img/logo-unissex.svg'); ?>" alt="Logo padrão">
                                <?php endif; ?>
                            </div>
                            <div class="salon-preview__brand">
                                <span class="salon-preview__name"><?php echo htmlspecialchars($dadosForm['nome_fantasia'] ?? ($dadosForm['nome'] ?? 'Seu salão')); ?></span>
                                <span class="salon-preview__tagline">Experiência personalizada</span>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>

        <div class="salon-actions">
            <a class="btn btn-outline-light" href="<?php echo base_url('dashboard'); ?>">Descartar</a>
            <button type="submit" class="btn btn-gradient"><i class="bi bi-save me-2"></i>Salvar alterações</button>
        </div>
    </form>
</div>
