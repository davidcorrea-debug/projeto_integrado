<!-- Mensagem de feedback -->
<?php if (!empty($msg)) echo $msg; ?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div class="agenda-hero">
        <p class="agenda-hero__title mb-2">Gerencie os dados e o histórico dos seus clientes</p>
        <div class="agenda-hero__bar"></div>
    </div>
    <a href="<?php echo base_url('clientes/novo'); ?>" class="btn btn-primary rounded-pill px-4 shadow-sm">
        <i class="bi bi-person-plus-fill me-1"></i> Novo Cliente
    </a>
</div>

<!-- Filtro de busca -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo base_url('clientes'); ?>" class="row g-2 align-items-end">
            <div class="col-md-9">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="busca" class="form-control bg-light border-start-0"
                           placeholder="Buscar por nome, telefone ou e-mail..."
                           value="<?php echo htmlspecialchars($busca ?? ''); ?>">
                </div>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Buscar</button>
                <?php if (!empty($busca)): ?>
                    <a href="<?php echo base_url('clientes'); ?>" class="btn btn-outline-secondary">Limpar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Tabela de Clientes -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Nome</th>
                        <th>Contato</th>
                        <th>Nascimento</th>
                        <th>Cadastrado em</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($clientes)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-1 d-block mb-2"></i>
                                Nenhum cliente encontrado.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($clientes as $c): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle text-center me-3 fw-bold"
                                         style="width:40px;height:40px;line-height:40px;font-size:0.8rem;">
                                        <?php echo mb_strtoupper(mb_substr($c['cliente_nome'], 0, 2)); ?>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-semibold text-dark"><?php echo htmlspecialchars($c['cliente_nome']); ?></h6>
                                        <small class="text-muted"><?php echo htmlspecialchars($c['cliente_email'] ?? ''); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($c['cliente_telefone'])): ?>
                                    <i class="bi bi-whatsapp text-success me-1"></i>
                                    <?php echo htmlspecialchars($c['cliente_telefone']); ?>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo !empty($c['cliente_nascimento']) ? formatarData($c['cliente_nascimento']) : '—'; ?>
                            </td>
                            <td class="text-muted small">
                                <?php echo formatarData($c['criado_em']); ?>
                            </td>
                            <td class="text-end pe-4">
                                <a href="<?php echo base_url('clientes/editar/' . $c['cliente_id']); ?>"
                                   class="btn btn-sm btn-light text-secondary rounded-pill me-1" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?php echo base_url('clientes/excluir/' . $c['cliente_id']); ?>"
                                   class="btn btn-sm btn-light text-danger rounded-pill"
                                   title="Excluir"
                                   onclick="return confirm('Excluir este cliente?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-top py-3">
        <small class="text-muted">Total: <?php echo count($clientes); ?> cliente(s) encontrado(s).</small>
    </div>
</div>
