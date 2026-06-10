<?php if (!empty($msg)) echo $msg; ?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-semibold"><?php echo htmlspecialchars($pagina ?? 'Agendamento'); ?></h5>
                <small class="text-muted">Lembre-se: alterações de data/hora são permitidas até 2 horas antes do compromisso.</small>
            </div>
            <div class="card-body p-4">
                <form action="<?php echo htmlspecialchars($action); ?>" method="POST" class="needs-validation" novalidate>

                    <!-- Serviço -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">Serviço <span class="text-danger">*</span></label>
                        <select name="servico_id" class="form-select" required>
                            <option value="">Selecione o serviço...</option>
                            <?php foreach ($servicos as $s): ?>
                                <option value="<?php echo $s['servico_id']; ?>"
                                        data-preco="<?php echo number_format($s['servico_preco'], 2, ',', '.'); ?>"
                                        data-duracao="<?php echo formatarDuracao($s['servico_duracao']); ?>"
                                        data-duracao-min="<?php echo (int)$s['servico_duracao']; ?>"
                                        <?php echo ((int)($agendamento['servico_id'] ?? 0) === (int)$s['servico_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($s['servico_nome']); ?> — <?php echo formatarDinheiro($s['servico_preco']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Profissional -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">Profissional <span class="text-danger">*</span></label>
                        <select name="usuario_id" class="form-select" required>
                            <option value="">Selecione o profissional...</option>
                            <?php foreach ($profissionais as $p): ?>
                                <option value="<?php echo $p['usuario_id']; ?>"
                                        <?php echo ((int)($agendamento['usuario_id'] ?? 0) === (int)$p['usuario_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($p['usuario_nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Data e Hora -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Data <span class="text-danger">*</span></label>
                            <input type="date" name="agendamento_data" class="form-control"
                                   value="<?php echo htmlspecialchars($agendamento['agendamento_data'] ?? date('Y-m-d')); ?>"
                                   min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Horário <span class="text-danger">*</span></label>
                            <input type="time" name="agendamento_hora" class="form-control"
                                   value="<?php echo htmlspecialchars($agendamento['agendamento_hora'] ?? '08:00'); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3 availability-helper"
                         data-availability
                         data-url="<?php echo base_url('cliente/agendamentos/disponibilidades'); ?>"
                         data-agendamento-id="<?php echo (int)($agendamento['agendamento_id'] ?? 0); ?>">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-medium">Horários disponíveis</span>
                            <small class="text-muted" data-availability-status>Selecione serviço, profissional e data.</small>
                        </div>
                        <div class="availability-helper__slots" data-availability-slots></div>
                        <small class="text-muted d-block mt-2">Os horários consideram a duração completa do serviço escolhido.</small>
                    </div>

                    <!-- Observações -->
                    <div class="mb-4">
                        <label class="form-label fw-medium">Observações</label>
                        <textarea name="agendamento_obs" class="form-control" rows="3"
                                  placeholder="Alguma informação importante?"><?php echo htmlspecialchars($agendamento['agendamento_obs'] ?? ''); ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-check2-circle me-1"></i> Salvar
                        </button>
                        <a href="<?php echo base_url('cliente/agendamentos'); ?>" class="btn btn-outline-secondary rounded-pill px-4">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const availabilityRoot = document.querySelector('[data-availability]');
    if (!availabilityRoot) {
        return;
    }

    const servicoSelect = document.querySelector('select[name="servico_id"]');
    const profissionalSelect = document.querySelector('select[name="usuario_id"]');
    const dataInput = document.querySelector('input[name="agendamento_data"]');
    const horaInput = document.querySelector('input[name="agendamento_hora"]');
    const slotsWrapper = availabilityRoot.querySelector('[data-availability-slots]');
    const statusEl = availabilityRoot.querySelector('[data-availability-status]');
    const endpoint = availabilityRoot.dataset.url;
    const agendamentoId = availabilityRoot.dataset.agendamentoId || '';

    let ultimoSelecionado = horaInput ? horaInput.value : '';

    const marcarSelecionado = (valor) => {
        if (!slotsWrapper) return;
        slotsWrapper.querySelectorAll('.availability-slot').forEach((btn) => {
            if (btn.dataset.valor === valor) {
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-primary');
            } else {
                btn.classList.add('btn-outline-primary');
                btn.classList.remove('btn-primary');
            }
        });
    };

    const limparSlots = (mensagem) => {
        if (slotsWrapper) {
            slotsWrapper.innerHTML = '';
        }
        if (statusEl) {
            statusEl.textContent = mensagem;
        }
    };

    const obterDuracaoSelecionada = () => {
        if (!servicoSelect || !servicoSelect.selectedOptions.length) {
            return 0;
        }
        const option = servicoSelect.selectedOptions[0];
        const valor = parseInt(option.dataset.duracaoMin ?? option.dataset.duracao ?? '0', 10);
        return Number.isFinite(valor) && valor > 0 ? valor : 0;
    };

    const carregarDisponibilidades = async () => {
        if (!endpoint) {
            return;
        }

        const servicoId = parseInt(servicoSelect?.value ?? '0', 10) || 0;
        const profissionalId = parseInt(profissionalSelect?.value ?? '0', 10) || 0;
        const dataSelecionada = dataInput?.value ?? '';
        const duracao = obterDuracaoSelecionada();

        if (!servicoId || !profissionalId || !dataSelecionada || !duracao) {
            limparSlots('Selecione serviço, profissional e data.');
            return;
        }

        if (statusEl) {
            statusEl.textContent = 'Carregando horários disponíveis...';
        }
        if (slotsWrapper) {
            slotsWrapper.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"></div>';
        }

        const params = new URLSearchParams({
            usuario_id: profissionalId.toString(),
            servico_id: servicoId.toString(),
            data: dataSelecionada,
        });

        if (agendamentoId) {
            params.append('agendamento_id', agendamentoId);
        }

        try {
            const response = await fetch(`${endpoint}?${params.toString()}`, {
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error('Erro ao carregar disponibilidades');
            }

            const payload = await response.json();
            const horarios = Array.isArray(payload?.horarios) ? payload.horarios : [];

            if (!horarios.length) {
                limparSlots('Nenhum horário disponível para os filtros informados.');
                return;
            }

            if (slotsWrapper) {
                slotsWrapper.innerHTML = '';
                horarios.forEach((hora) => {
                    const botao = document.createElement('button');
                    botao.type = 'button';
                    botao.className = 'btn btn-outline-primary btn-sm availability-slot';
                    botao.textContent = hora;
                    botao.dataset.valor = hora;
                    botao.addEventListener('click', () => {
                        if (horaInput) {
                            horaInput.value = hora;
                        }
                        ultimoSelecionado = hora;
                        marcarSelecionado(hora);
                    });
                    slotsWrapper.appendChild(botao);
                });
            }

            if (statusEl) {
                statusEl.textContent = 'Escolha um dos horários sugeridos:';
            }

            const referencia = horaInput?.value || ultimoSelecionado;
            if (referencia) {
                marcarSelecionado(referencia);
            }
        } catch (erro) {
            limparSlots('Não foi possível carregar os horários disponíveis. Tente novamente.');
        }
    };

    [servicoSelect, profissionalSelect, dataInput].forEach((campo) => {
        if (campo) {
            campo.addEventListener('change', carregarDisponibilidades);
        }
    });

    if (horaInput) {
        horaInput.addEventListener('change', () => {
            ultimoSelecionado = horaInput.value;
            marcarSelecionado(ultimoSelecionado);
        });
    }

    carregarDisponibilidades();
});
</script>
