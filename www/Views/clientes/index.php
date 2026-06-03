<!-- Mensagem de feedback -->
<?php if (!empty($msg))
    echo $msg; ?>

<!-- Header Atrativo -->
<div class="mb-5"
    style="background: linear-gradient(135deg, #d63384 0%, #b02a6c 100%); border-radius: 1.5rem; padding: 3rem 2rem; color: white;">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <div style="font-size: 3rem; margin-bottom: 1rem;">👥</div>
            <h2 class="mb-2 fw-bold" style="font-size: 2.5rem;">Meus Clientes</h2>
            <p class="mb-0" style="font-size: 1.1rem; opacity: 0.95;">Gerencie os dados e o histórico dos seus clientes
                da melhor forma</p>
        </div>
        <a href="<?php echo base_url('clientes/novo'); ?>" class="btn btn-light shadow-lg"
            style="font-weight: 600; padding: 0.75rem 2rem; border-radius: 50px; color: #d63384; font-size: 1.05rem;">
            <i class="bi bi-person-plus-fill me-2"></i> Novo Cliente
        </a>
    </div>
</div>

<!-- Filtro de busca melhorado -->
<div class="mb-5">
    <form method="GET" action="<?php echo base_url('clientes'); ?>" class="row g-3">
        <div class="col-md-10">
            <div class="input-group input-group-lg shadow-sm" style="border-radius: 10px; overflow: hidden;">
                <span class="input-group-text bg-white border-end-0" style="color: #d63384;"><i
                        class="bi bi-search fs-5"></i></span>
                <input type="text" name="busca" class="form-control border-start-0 py-3"
                    style="border-color: #e0e0e0; font-size: 1rem;"
                    placeholder="🔍 Buscar por nome, telefone ou e-mail..."
                    value="<?php echo htmlspecialchars($busca ?? ''); ?>">
            </div>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100 fw-bold py-3"
                style="background: linear-gradient(135deg, #d63384 0%, #b02a6c 100%); border: none; border-radius: 10px;">
                Buscar
            </button>
            <?php if (!empty($busca)): ?>
                <a href="<?php echo base_url('clientes'); ?>" class="btn btn-outline-secondary fw-bold py-3"
                    style="border-radius: 10px;">Limpar</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Grid de Clientes em Cards -->
<?php if (empty($clientes)): ?>
    <div class="text-center py-5 mt-5">
        <div style="font-size: 5rem; opacity: 0.3; margin-bottom: 1rem;">👥</div>
        <h3 class="text-muted fw-bold mb-2">Nenhum cliente encontrado</h3>
        <p class="text-muted mb-4">Comece adicionando seu primeiro cliente ao sistema</p>
        <a href="<?php echo base_url('clientes/novo'); ?>" class="btn btn-primary btn-lg px-5"
            style="background: linear-gradient(135deg, #d63384 0%, #b02a6c 100%); border: none; border-radius: 10px;">
            <i class="bi bi-person-plus-fill me-2"></i> Novo Cliente
        </a>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php
        $cores_avatar = ['#d63384', '#ff6b9d', '#c2185b', '#e91e63', '#f06292', '#ec407a', '#e63384', '#ba1b6c'];
        $index = 0;
        foreach ($clientes as $c):
            $cor_avatar = $cores_avatar[$index % count($cores_avatar)];
            $iniciais = mb_strtoupper(mb_substr($c['cliente_nome'], 0, 2));
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm overflow-hidden h-100"
                    style="transition: all 0.3s ease; border-radius: 1.5rem; cursor: pointer;"
                    onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 12px 24px rgba(214, 51, 132, 0.2)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 0.125rem 0.25rem rgba(0, 0, 0, 0.075)';">

                    <!-- Header do Card com Gradiente -->
                    <div
                        style="background: linear-gradient(135deg, <?php echo $cor_avatar; ?> 0%, <?php echo $cor_avatar; ?>dd 100%); padding: 1.5rem; color: white;">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                style="width: 60px; height: 60px; background-color: rgba(255,255,255,0.2); font-size: 1.5rem; border: 3px solid rgba(255,255,255,0.4);">
                                <?php echo $iniciais; ?>
                            </div>
                            <span class="badge bg-white text-dark fw-bold" style="font-size: 0.8rem;">✨ Cliente</span>
                        </div>
                        <h5 class="mb-0 fw-bold" style="font-size: 1.3rem;"><?php echo htmlspecialchars($c['cliente_nome']); ?>
                        </h5>
                    </div>

                    <!-- Corpo do Card -->
                    <div class="card-body">
                        <!-- Email -->
                        <div class="mb-3 d-flex align-items-center">
                            <i class="bi bi-envelope me-2" style="color: #d63384; font-size: 1.1rem;"></i>
                            <small class="text-muted">
                                <?php echo !empty($c['cliente_email']) ? htmlspecialchars($c['cliente_email']) : 'Sem email'; ?>
                            </small>
                        </div>

                        <!-- Telefone -->
                        <div class="mb-3 d-flex align-items-center">
                            <i class="bi bi-whatsapp me-2" style="color: #25d366; font-size: 1.1rem;"></i>
                            <small class="text-muted">
                                <?php echo !empty($c['cliente_telefone']) ? htmlspecialchars($c['cliente_telefone']) : 'Sem telefone'; ?>
                            </small>
                        </div>

                        <!-- Data de Nascimento -->
                        <div class="mb-3 d-flex align-items-center">
                            <i class="bi bi-calendar-heart me-2" style="color: #ff6b9d; font-size: 1.1rem;"></i>
                            <small class="text-muted">
                                <?php echo !empty($c['cliente_nascimento']) ? formatarData($c['cliente_nascimento']) : 'Sem data'; ?>
                            </small>
                        </div>

                        <!-- Data de Cadastro -->
                        <div class="d-flex align-items-center mb-4">
                            <i class="bi bi-calendar-check me-2" style="color: #7c3aed; font-size: 1.1rem;"></i>
                            <small class="text-muted">
                                Cadastrado: <?php echo formatarData($c['criado_em']); ?>
                            </small>
                        </div>

                        <!-- Observações se houver -->
                        <?php if (!empty($c['cliente_observacoes'])): ?>
                            <div class="mb-3 p-2 rounded" style="background-color: #f8f9fa; border-left: 4px solid #d63384;">
                                <small class="text-muted d-block" style="font-size: 0.85rem; max-height: 60px; overflow: hidden;">
                                    💡 <?php echo htmlspecialchars(mb_substr($c['cliente_observacoes'], 0, 80)); ?>...
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Footer do Card com Ações -->
                    <div class="card-footer bg-white border-top d-flex gap-2 pt-3 pb-3">
                        <a href="<?php echo base_url('clientes/editar/' . $c['cliente_id']); ?>" class="btn btn-sm flex-grow-1"
                            style="background: linear-gradient(135deg, #d63384 0%, #b02a6c 100%); color: white; border: none; border-radius: 8px; font-weight: 600; transition: all 0.2s;">
                            <i class="bi bi-pencil me-1"></i> Editar
                        </a>
                        <a href="<?php echo base_url('clientes/excluir/' . $c['cliente_id']); ?>"
                            class="btn btn-sm btn-outline-danger" style="border-radius: 8px; font-weight: 600;" title="Excluir"
                            onclick="return confirm('Excluir este cliente?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php
            $index++;
        endforeach;
        ?>
    </div>

    <!-- Resumo de Clientes -->
    <div class="mt-5 pt-4 border-top">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="text-center p-4 rounded"
                    style="background: linear-gradient(135deg, #d63384 0%, #b02a6c 100%); color: white;">
                    <div style="font-size: 2.5rem; font-weight: bold;"><?php echo count($clientes); ?></div>
                    <small>Cliente(s) Cadastrado(s)</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center p-4 rounded"
                    style="background: linear-gradient(135deg, #ff6b9d 0%, #ff4081 100%); color: white;">
                    <div style="font-size: 2.5rem; font-weight: bold;">
                        <?php echo count(array_filter($clientes, function ($c) {
                            return !empty($c['cliente_telefone']); })); ?>
                    </div>
                    <small>Com Telefone Registrado</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center p-4 rounded"
                    style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); color: white;">
                    <div style="font-size: 2.5rem; font-weight: bold;">
                        <?php echo count(array_filter($clientes, function ($c) {
                            return !empty($c['cliente_email']); })); ?>
                    </div>
                    <small>Com Email Registrado</small>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>