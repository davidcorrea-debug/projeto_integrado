<div class="row g-4">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h2 class="h4 fw-semibold mb-3">Informações do Estabelecimento</h2>
                <p class="text-muted mb-4">Atualize os dados exibidos para clientes nas telas públicas e no portal.</p>

                <?php if (!empty($msg)): ?>
                    <div class="mb-3"><?php echo $msg; ?></div>
                <?php endif; ?>

                <?php if (!empty($erros)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($erros as $erro): ?>
                                <li><?php echo htmlspecialchars($erro); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php
                    $dadosForm = array_merge(
                        (array)($estabelecimento ?? []),
                        (array)($dados ?? [])
                    );
                ?>

                <?php $logoAtual = $dadosForm['logo'] ?? ($estabelecimento['logo'] ?? ''); ?>

                <form action="<?php echo base_url('estabelecimento/salvar'); ?>" method="POST" enctype="multipart/form-data" class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-medium">Nome do salão *</label>
                        <input type="text" class="form-control" name="nome" value="<?php echo htmlspecialchars($dadosForm['nome'] ?? ''); ?>" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-medium">Nome fantasia</label>
                        <input type="text" class="form-control" name="nome_fantasia" value="<?php echo htmlspecialchars($dadosForm['nome_fantasia'] ?? ''); ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">Telefone *</label>
                        <input type="text" class="form-control" name="telefone" value="<?php echo htmlspecialchars($dadosForm['telefone'] ?? ''); ?>" placeholder="(00) 00000-0000" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">E-mail</label>
                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($dadosForm['email'] ?? ''); ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-medium">Endereço completo *</label>
                        <textarea class="form-control" name="endereco" rows="2" placeholder="Rua, número, bairro, cidade - UF" required><?php echo htmlspecialchars($dadosForm['endereco'] ?? ''); ?></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">CEP *</label>
                        <input type="text" class="form-control" name="cep" value="<?php echo htmlspecialchars($dadosForm['cep'] ?? ''); ?>" placeholder="00000-000" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">CNPJ *</label>
                        <input type="text" class="form-control" name="cnpj" value="<?php echo htmlspecialchars($dadosForm['cnpj'] ?? ''); ?>" placeholder="00.000.000/0000-00" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-medium">Link de localização (Google Maps)</label>
                        <input type="url" class="form-control" name="localizacao_url" value="<?php echo htmlspecialchars($dadosForm['localizacao_url'] ?? ''); ?>" placeholder="https://maps.google.com/...">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-medium">Instagram</label>
                        <input type="url" class="form-control" name="instagram" value="<?php echo htmlspecialchars($dadosForm['instagram'] ?? ''); ?>" placeholder="https://instagram.com/...">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-medium">Facebook</label>
                        <input type="url" class="form-control" name="facebook" value="<?php echo htmlspecialchars($dadosForm['facebook'] ?? ''); ?>" placeholder="https://facebook.com/...">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-medium">Site</label>
                        <input type="url" class="form-control" name="site" value="<?php echo htmlspecialchars($dadosForm['site'] ?? ''); ?>" placeholder="https://...">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-medium">Foto do estabelecimento</label>
                        <?php if (!empty($logoAtual)): ?>
                            <div class="mb-3">
                                <img src="<?php echo base_url($logoAtual); ?>" alt="Foto atual do estabelecimento" class="img-fluid rounded border" style="max-height: 220px; object-fit: cover;">
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="remover_logo" id="removerLogo">
                                <label class="form-check-label" for="removerLogo">Remover foto atual</label>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" name="logo" accept="image/jpeg,image/png,image/webp">
                        <small class="text-muted">Formatos permitidos: JPG, PNG ou WEBP (até 2MB).</small>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2 pt-3">
                        <a class="btn btn-outline-secondary" href="<?php echo base_url('dashboard'); ?>">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Salvar alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3">Dicas</h5>
                <p class="text-muted small mb-3">Estas informações aparecem nas telas de login e comunicação com clientes. Mantenha-as sempre atualizadas para reforçar a identidade do salão e facilitar o contato.</p>
                <ul class="small text-muted ps-3 mb-0">
                    <li>Use o nome fantasia que os clientes reconhecem.</li>
                    <li>Inclua o link do Google Maps para facilitar a navegação.</li>
                    <li>Atualize redes sociais ativas para engajar a comunidade.</li>
                    <li>Verifique o CNPJ e telefone antes de salvar.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
