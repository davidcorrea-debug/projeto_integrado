<?php 
if (!empty($msg)) {
    echo $msg;
    $detalhes = $_SESSION['form_profissional_erro'] ?? [];
    unset($_SESSION['form_profissional_erro']);
    $jsonMsg = json_encode($msg);
    $jsonDetalhes = json_encode($detalhes);
    echo '<script>console.log("%c[PROF-INDEX] Message displayed after redirect", "color: red; font-weight: bold;"); console.log("Message HTML:", ' . $jsonMsg . '); console.log("Validation details:", ' . $jsonDetalhes . ');</script>';
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <form class="d-flex" method="GET" action="<?php echo base_url('profissionais'); ?>">
        <input type="text" class="form-control me-2" name="busca" placeholder="Buscar por nome ou e-mail" value="<?php echo htmlspecialchars($busca ?? ''); ?>"/>
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
    </form>
    <a href="<?php echo base_url('profissionais/novo'); ?>" class="btn btn-primary rounded-pill px-4 shadow-sm">
        <i class="bi bi-person-plus me-1"></i> Novo Profissional
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Perfil</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($profissionais)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Nenhum profissional encontrado.</td></tr>
                    <?php else: ?>
                        <?php foreach ($profissionais as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['usuario_nome']); ?></td>
                                <td><?php echo htmlspecialchars($p['usuario_email']); ?></td>
                                <td><span class="badge bg-secondary">Profissional</span></td>
                                <td>
                                    <?php if ((int)($p['usuario_ativo'] ?? 1) === 1): ?>
                                        <span class="badge bg-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="<?php echo base_url('profissionais/' . $p['usuario_id'] . '/horarios'); ?>" class="btn btn-outline-primary btn-sm rounded-pill">
                                        <i class="bi bi-clock-history me-1"></i> Ajustar expediente
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
