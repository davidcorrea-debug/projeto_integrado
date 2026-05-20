<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted mb-0">Controle a agenda e visualize os compromissos</p>
    </div>
    <a href="<?php echo base_url('agendamentos/novo'); ?>" class="btn btn-primary rounded-pill px-4 shadow-sm">
        <i class="bi bi-calendar-plus me-1"></i> Novo Agendamento
    </a>
</div>

<!-- Navegação do Calendário e Filtros -->
<div class="row mb-4 align-items-center">
    <div class="col-md-4">
        <div class="btn-group shadow-sm" role="group">
            <button type="button" class="btn btn-outline-secondary"><i class="bi bi-chevron-left"></i></button>
            <button type="button" class="btn btn-outline-secondary fw-semibold px-4">18 de Maio, 2026</button>
            <button type="button" class="btn btn-outline-secondary"><i class="bi bi-chevron-right"></i></button>
        </div>
    </div>
    <div class="col-md-8 d-flex justify-content-md-end mt-3 mt-md-0">
        <div class="btn-group me-3 shadow-sm" role="group">
            <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked>
            <label class="btn btn-outline-secondary" for="btnradio1">Lista</label>

            <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
            <label class="btn btn-outline-secondary" for="btnradio2">Dia</label>

            <input type="radio" class="btn-check" name="btnradio" id="btnradio3" autocomplete="off">
            <label class="btn btn-outline-secondary" for="btnradio3">Semana</label>
        </div>
        <select class="form-select bg-light w-auto shadow-sm">
            <option selected>Todos os Profissionais</option>
            <option value="1">Ana Silva</option>
            <option value="2">Carla Mendes</option>
            <option value="3">Luiza Costa</option>
        </select>
    </div>
</div>

<!-- Lista de Agendamentos do Dia -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="list-group list-group-flush rounded-3">
            
            <!-- Item de Agendamento 1 -->
            <div class="list-group-item p-4 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <div class="text-center me-4" style="min-width: 60px;">
                        <h4 class="fw-bold mb-0 text-dark">14:00</h4>
                        <small class="text-muted">60 min</small>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle text-center me-3 fw-bold" style="width: 48px; height: 48px; line-height: 48px;">MA</div>
                    <div>
                        <h6 class="mb-1 fw-semibold text-dark">Maria Almeida</h6>
                        <div class="text-muted small mb-1"><i class="bi bi-scissors me-1"></i> Corte + Hidratação</div>
                        <div class="text-muted small"><i class="bi bi-person-badge me-1"></i> Profissional: Ana Silva</div>
                    </div>
                </div>
                <div class="d-flex flex-column align-items-md-end">
                    <span class="badge bg-warning text-dark rounded-pill mb-2 px-3 py-2">Aguardando</span>
                    <div>
                        <button class="btn btn-sm btn-outline-success rounded-pill me-1"><i class="bi bi-check-lg"></i> Iniciar</button>
                        <button class="btn btn-sm btn-outline-secondary rounded-pill"><i class="bi bi-three-dots"></i></button>
                    </div>
                </div>
            </div>

            <!-- Item de Agendamento 2 -->
            <div class="list-group-item p-4 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center bg-light">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <div class="text-center me-4" style="min-width: 60px;">
                        <h4 class="fw-bold mb-0 text-dark">15:30</h4>
                        <small class="text-muted">180 min</small>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle text-center me-3 fw-bold" style="width: 48px; height: 48px; line-height: 48px;">JO</div>
                    <div>
                        <h6 class="mb-1 fw-semibold text-dark">Joana Oliveira</h6>
                        <div class="text-muted small mb-1"><i class="bi bi-scissors me-1"></i> Coloração Raiz</div>
                        <div class="text-muted small"><i class="bi bi-person-badge me-1"></i> Profissional: Carla Mendes</div>
                    </div>
                </div>
                <div class="d-flex flex-column align-items-md-end">
                    <span class="badge bg-success rounded-pill mb-2 px-3 py-2">Confirmado</span>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary rounded-pill me-1">Reagendar</button>
                        <button class="btn btn-sm btn-outline-danger rounded-pill"><i class="bi bi-x-lg"></i></button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
