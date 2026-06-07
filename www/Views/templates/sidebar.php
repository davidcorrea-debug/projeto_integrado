        <!-- Sidebar -->
        <div class="bg-white shadow-sm" id="sidebar-wrapper" style="width: 250px; min-height: 100vh;">
            <div class="sidebar-heading text-center py-4 border-bottom">
                <h4 class="text-primary fw-bold mb-0"><i class="bi bi-stars"></i> Glow Agenda</h4>
            </div>
            <div class="list-group list-group-flush my-3">
                <a href="<?php echo base_url('dashboard'); ?>" class="list-group-item list-group-item-action bg-transparent border-0 px-4 py-3 fw-medium <?php echo (strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false) ? 'active text-primary' : 'text-secondary'; ?>">
                    <i class="bi bi-grid-fill me-2"></i> Dashboard
                </a>
                <?php $role = $_SESSION['usuario_perfil'] ?? ''; ?>
                <?php
                    $agLink   = $role === 'cliente' ? 'cliente/agendamentos' : 'agendamentos';
                    $agActive = strpos($_SERVER['REQUEST_URI'], $agLink) !== false;
                    $agLabel  = $role === 'cliente' ? 'Meus agendamentos' : 'Agendamentos';
                ?>
                <a href="<?php echo base_url($agLink); ?>" class="list-group-item list-group-item-action bg-transparent border-0 px-4 py-3 fw-medium <?php echo $agActive ? 'active text-primary' : 'text-secondary'; ?>">
                    <i class="bi bi-calendar-event-fill me-2"></i> <?php echo $agLabel; ?>
                </a>
                <?php if ($role === 'admin' || $role === 'profissional'): ?>
                    <a href="<?php echo base_url('servicos'); ?>" class="list-group-item list-group-item-action bg-transparent border-0 px-4 py-3 fw-medium <?php echo (strpos($_SERVER['REQUEST_URI'], 'servicos') !== false) ? 'active text-primary' : 'text-secondary'; ?>">
                        <i class="bi bi-scissors me-2"></i> Serviços
                    </a>
                    <a href="<?php echo base_url('clientes'); ?>" class="list-group-item list-group-item-action bg-transparent border-0 px-4 py-3 fw-medium <?php echo (strpos($_SERVER['REQUEST_URI'], 'clientes') !== false) ? 'active text-primary' : 'text-secondary'; ?>">
                        <i class="bi bi-people-fill me-2"></i> Clientes
                    </a>
                <?php endif; ?>
                <?php if ($role === 'admin'): ?>
                    <a href="<?php echo base_url('profissionais'); ?>" class="list-group-item list-group-item-action bg-transparent border-0 px-4 py-3 fw-medium <?php echo (strpos($_SERVER['REQUEST_URI'], 'profissionais') !== false) ? 'active text-primary' : 'text-secondary'; ?>">
                        <i class="bi bi-person-gear me-2"></i> Profissionais
                    </a>
                    <a href="<?php echo base_url('estabelecimento'); ?>" class="list-group-item list-group-item-action bg-transparent border-0 px-4 py-3 fw-medium <?php echo (strpos($_SERVER['REQUEST_URI'], 'estabelecimento') !== false) ? 'active text-primary' : 'text-secondary'; ?>">
                        <i class="bi bi-building me-2"></i> Meu Salão
                    </a>
                <?php endif; ?>
                
                <hr class="mx-3 text-secondary">
                
                <?php $configActive = strpos($_SERVER['REQUEST_URI'], 'configuracoes') !== false; ?>
                <a href="<?php echo base_url('configuracoes'); ?>" class="list-group-item list-group-item-action bg-transparent border-0 px-4 py-3 fw-medium <?php echo $configActive ? 'active text-primary' : 'text-secondary'; ?>">
                    <i class="bi bi-gear-fill me-2"></i> Configurações
                </a>
                <a href="<?php echo base_url('logout'); ?>" class="list-group-item list-group-item-action bg-transparent border-0 px-4 py-3 fw-medium text-danger mt-5">
                    <i class="bi bi-box-arrow-right me-2"></i> Sair
                </a>
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper" class="w-100">
            
            <nav class="navbar navbar-expand-lg navbar-light bg-white py-3 px-4 shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="bi bi-list fs-3 text-secondary me-3" id="menu-toggle" style="cursor: pointer;"></i>
                    <h5 class="mb-0 fw-semibold text-dark"><?php echo isset($pagina) ? $pagina : 'Glow Agenda'; ?></h5>
                </div>
                
                <div class="ms-auto d-flex align-items-center">
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle text-dark fw-medium" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle fs-4 text-secondary me-1 align-middle"></i>
                            <?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário'); ?>
                            <small class="text-muted ms-1">(<?php echo htmlspecialchars($_SESSION['usuario_perfil'] ?? ''); ?>)</small>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="<?php echo base_url('configuracoes'); ?>"><i class="bi bi-person me-2"></i> Meu Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo base_url('logout'); ?>"><i class="bi bi-box-arrow-right me-2"></i> Sair</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="container-fluid p-4">
