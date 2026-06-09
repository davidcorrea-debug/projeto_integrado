<?php if (!empty($msg)) echo $msg; ?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div class="agenda-hero">
        <p class="agenda-hero__title mb-2">Gerencie os serviços oferecidos pelo salão</p>
        <div class="agenda-hero__bar"></div>
    </div>
    <a href="<?php echo base_url('servicos/novo'); ?>" class="btn btn-primary rounded-pill px-4 shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Novo Serviço
    </a>
</div>

<!-- Filtros -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo base_url('servicos'); ?>" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="busca" class="form-control bg-light"
                       placeholder="Buscar serviço por nome..."
                       value="<?php echo htmlspecialchars($busca ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <select name="categoria" class="form-select bg-light">
                    <option value="">Todas as Categorias</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat['categoria_id']; ?>"
                            <?php echo ($categoria == $cat['categoria_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['categoria_nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="ativo" class="form-select bg-light">
                    <option value="">Todos os Status</option>
                    <option value="1" <?php echo ($ativo === '1') ? 'selected' : ''; ?>>Ativo</option>
                    <option value="0" <?php echo ($ativo === '0') ? 'selected' : ''; ?>>Inativo</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Filtrar
                </button>
                <?php if (!empty($busca) || !empty($categoria) || $ativo !== ''): ?>
                    <a href="<?php echo base_url('servicos'); ?>" class="btn btn-outline-secondary">Limpar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Grid de Serviços -->
<div class="row g-4">
    <?php if (empty($servicos)): ?>
        <div class="col-12">
            <div class="card shadow-sm border-0 text-center py-5">
                <div class="card-body text-muted">
                    <i class="bi bi-scissors fs-1 d-block mb-3"></i>
                    Nenhum serviço encontrado.
                    <a href="<?php echo base_url('servicos/novo'); ?>" class="d-block mt-2">Cadastrar primeiro serviço</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($servicos as $s): ?>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm h-100 <?php echo !$s['servico_ativo'] ? 'opacity-75' : ''; ?>">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">
                            <?php echo htmlspecialchars($s['categoria_nome']); ?>
                        </span>
                        <?php if ($s['servico_ativo']): ?>
                            <span class="badge bg-success rounded-pill">Ativo</span>
                        <?php else: ?>
                            <span class="badge bg-secondary rounded-pill">Inativo</span>
                        <?php endif; ?>
                    </div>
                    <h5 class="card-title fw-bold mt-2"><?php echo htmlspecialchars($s['servico_nome']); ?></h5>
                    <?php if (!empty($s['servico_descricao'])): ?>
                        <p class="card-text text-muted small mb-3">
                            <?php echo htmlspecialchars($s['servico_descricao']); ?>
                        </p>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <span class="fs-5 fw-semibold text-dark">
                            <?php echo formatarDinheiro($s['servico_preco']); ?>
                        </span>
                        <span class="text-muted small">
                            <i class="bi bi-clock me-1"></i>
                            <?php echo formatarDuracao($s['servico_duracao']); ?>
                        </span>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 d-flex gap-2 pb-3">
                    <a href="<?php echo base_url('servicos/editar/' . $s['servico_id']); ?>"
                       class="btn btn-sm btn-outline-secondary rounded-pill">
                        <i class="bi bi-pencil me-1"></i> Editar
                    </a>
                    <a href="<?php echo base_url('servicos/excluir/' . $s['servico_id']); ?>"
                       class="btn btn-sm btn-outline-danger rounded-pill"
                       onclick="return confirm('Excluir este serviço?')">
                        <i class="bi bi-trash"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
