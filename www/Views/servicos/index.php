<?php if (!empty($msg)) echo $msg; ?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div class="agenda-hero">
        <p class="agenda-hero__title mb-2">Gerencie os serviços oferecidos pelo salão</p>
        <div class="agenda-hero__bar"></div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?php echo base_url('servicos/novo'); ?>" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Novo Serviço
        </a>
        <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm"
                data-bs-toggle="modal" data-bs-target="#modalNovaCategoria">
            <i class="bi bi-tags-fill me-1"></i> Nova Categoria
        </button>
    </div>
</div>

<!-- Modal Nova Categoria -->
<div class="modal fade" id="modalNovaCategoria" tabindex="-1" aria-labelledby="modalNovaCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg category-modal">
            <form action="<?php echo base_url('servicos/categorias/criar'); ?>" method="POST">
                <div class="modal-header border-0 pb-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="category-modal__icon">
                            <i class="bi bi-palette-fill"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-1" id="modalNovaCategoriaLabel">Cadastrar nova categoria</h5>
                            <p class="text-muted mb-0 small">Organize os serviços por segmento antes de disponibilizá-los para agendamento.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Segmento</label>
                        <div class="btn-group w-100 category-modal__segmento" role="group">
                            <input type="radio" class="btn-check" name="categoria_segmento" id="segmentoBarbearia" value="Barbearia" checked>
                            <label class="btn" for="segmentoBarbearia">Barbearia</label>

                            <input type="radio" class="btn-check" name="categoria_segmento" id="segmentoSalao" value="Salão">
                            <label class="btn" for="segmentoSalao">Salão</label>

                            <input type="radio" class="btn-check" name="categoria_segmento" id="segmentoUnissex" value="Unissex">
                            <label class="btn" for="segmentoUnissex">Unissex</label>
                        </div>
                        <small class="text-muted d-block mt-2">Esse segmento será usado como sugestão ao cadastrar novos serviços.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nome da categoria <span class="text-danger">*</span></label>
                        <div class="input-group category-modal__input">
                            <span class="input-group-text"><i class="bi bi-type"></i></span>
                            <input type="text" name="categoria_nome" class="form-control" placeholder="Ex: Corte degradê premium" required>
                        </div>
                        <small class="text-muted d-block mt-1">O nome ficará disponível imediatamente para seleção em novos serviços.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i> Salvar categoria
                    </button>
                </div>
            </form>
        </div>
    </div>
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
