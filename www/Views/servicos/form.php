<?php if (!empty($msg)) echo $msg; ?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-semibold"><?php echo htmlspecialchars($pagina); ?></h5>
            </div>
            <div class="card-body p-4">
                <?php
                    $action = !empty($servico['servico_id'])
                        ? base_url('servicos/atualizar/' . $servico['servico_id'])
                        : base_url('servicos/salvar');
                ?>
                <form action="<?php echo $action; ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nome do Serviço <span class="text-danger">*</span></label>
                        <input type="text" name="servico_nome" class="form-control"
                               placeholder="Ex: Corte Feminino"
                               value="<?php echo htmlspecialchars($servico['servico_nome'] ?? ''); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Categoria <span class="text-danger">*</span></label>
                        <select name="categoria_id" class="form-select servico-form__categoria" required>
                            <option value="">Selecione uma categoria...</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo $cat['categoria_id']; ?>"
                                    <?php echo (($servico['categoria_id'] ?? '') == $cat['categoria_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['categoria_nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted d-block mt-2">Não encontrou? Cadastre uma nova categoria abaixo.</small>
                        <input type="text" name="nova_categoria" class="form-control mt-2"
                               placeholder="Ex: Maquiagem social" value="">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Descrição</label>
                        <textarea name="servico_descricao" class="form-control" rows="3"
                                  placeholder="Descreva brevemente o serviço..."><?php echo htmlspecialchars($servico['servico_descricao'] ?? ''); ?></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Preço (R$) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">R$</span>
                                <input type="number" name="servico_preco" class="form-control"
                                       step="0.01" min="0" placeholder="0,00"
                                       value="<?php echo $servico['servico_preco'] ?? ''; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Duração (minutos) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="servico_duracao" class="form-control"
                                       min="1" placeholder="Ex: 60"
                                       value="<?php echo $servico['servico_duracao'] ?? ''; ?>" required>
                                <span class="input-group-text bg-light">min</span>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($servico['servico_id'])): ?>
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="servico_ativo"
                                   id="servico_ativo" value="1"
                                   <?php echo ($servico['servico_ativo'] ?? 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label fw-medium" for="servico_ativo">
                                Serviço Ativo
                            </label>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-check-lg me-1"></i>
                            <?php echo !empty($servico['servico_id']) ? 'Atualizar' : 'Cadastrar'; ?>
                        </button>
                        <a href="<?php echo base_url('servicos'); ?>" class="btn btn-outline-secondary rounded-pill px-4">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
