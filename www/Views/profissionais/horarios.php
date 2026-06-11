<?php if (!empty($msg)) echo $msg; ?>

<div class="row justify-content-center">
    <div class="col-12 col-xl-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 fw-semibold"><?php echo htmlspecialchars($pagina ?? 'Expediente'); ?></h5>
                    <small class="text-muted">Defina os horários de atendimento por dia da semana.</small>
                </div>
                <?php if (!empty($profissional)): ?>
                    <div class="text-end">
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2">
                            <i class="bi bi-person-bounding-box"></i>
                            <?php echo htmlspecialchars($profissional['usuario_nome'] ?? ''); ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-body p-4">
                <form action="<?php echo htmlspecialchars($action ?? '#'); ?>" method="POST" id="form-horarios">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Dia</th>
                                    <th class="text-center">Atende?</th>
                                    <th>Início</th>
                                    <th>Término</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($diasSemana as $indice => $nomeDia):
                                    $dadosDia = $horarios[$indice] ?? ['ativo' => true, 'inicio' => '08:00', 'fim' => '20:00'];
                                    $ativo = !empty($dadosDia['ativo']);
                                ?>
                                    <tr data-dia="<?php echo $indice; ?>">
                                        <td class="fw-medium"><?php echo htmlspecialchars($nomeDia); ?></td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-flex align-items-center justify-content-center">
                                                <input class="form-check-input toggle-ativo" type="checkbox" role="switch"
                                                       id="dia-<?php echo $indice; ?>-ativo" name="dias[<?php echo $indice; ?>][ativo]"
                                                       <?php echo $ativo ? 'checked' : ''; ?>>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="time"
                                                   class="form-control horario-inicio"
                                                   name="dias[<?php echo $indice; ?>][inicio]"
                                                   value="<?php echo htmlspecialchars($dadosDia['inicio']); ?>"
                                                   <?php echo $ativo ? '' : 'disabled'; ?>
                                                   required>
                                        </td>
                                        <td>
                                            <input type="time"
                                                   class="form-control horario-fim"
                                                   name="dias[<?php echo $indice; ?>][fim]"
                                                   value="<?php echo htmlspecialchars($dadosDia['fim']); ?>"
                                                   <?php echo $ativo ? '' : 'disabled'; ?>
                                                   required>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
                        <a href="<?php echo htmlspecialchars($voltar ?? base_url('dashboard')); ?>" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-arrow-left-circle me-1"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-save2 me-1"></i> Salvar horários
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('#form-horarios tr[data-dia]').forEach((linha) => {
            const toggle = linha.querySelector('.toggle-ativo');
            const inputs = linha.querySelectorAll('.horario-inicio, .horario-fim');

            const atualizarEstado = () => {
                const ativo = toggle.checked;
                inputs.forEach((input) => {
                    input.disabled = !ativo;
                });
            };

            toggle.addEventListener('change', atualizarEstado);
            atualizarEstado();
        });
    });
</script>
