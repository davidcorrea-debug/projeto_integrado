        <!-- Sidebar -->
        <div id="sidebar-wrapper" class="sidebar-wrapper">
            <div class="sidebar-brand">
                <div class="sidebar-brand__icon">
                    <i class="bi bi-moon-stars-fill"></i>
                </div>
                <div class="sidebar-brand__text">
                    <span class="sidebar-brand__title">Glow Agenda</span>
                    <span class="sidebar-brand__subtitle">Experiência premium</span>
                </div>
            </div>

            <?php $role = $_SESSION['usuario_perfil'] ?? ''; ?>
            <?php
                $dashboardActive = strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false;
                $agLink   = $role === 'cliente' ? 'cliente/agendamentos' : 'agendamentos';
                $agActive = strpos($_SERVER['REQUEST_URI'], $agLink) !== false;
                $agLabel  = $role === 'cliente' ? 'Meus agendamentos' : 'Agendamentos';
                $servicosActive = strpos($_SERVER['REQUEST_URI'], 'servicos') !== false;
                $clientesActive = strpos($_SERVER['REQUEST_URI'], 'clientes') !== false;
                $profActive     = strpos($_SERVER['REQUEST_URI'], 'profissionais') !== false;
                $estabActive    = strpos($_SERVER['REQUEST_URI'], 'estabelecimento') !== false;
                $configActive   = strpos($_SERVER['REQUEST_URI'], 'configuracoes') !== false;
            ?>

            <nav class="sidebar-nav">
                <a href="<?php echo base_url('dashboard'); ?>" class="sidebar-link <?php echo $dashboardActive ? 'is-active' : ''; ?>">
                    <i class="bi bi-grid-fill"></i>
                    <span>Dashboard</span>
                </a>

                <a href="<?php echo base_url($agLink); ?>" class="sidebar-link <?php echo $agActive ? 'is-active' : ''; ?>">
                    <i class="bi bi-calendar-event-fill"></i>
                    <span><?php echo $agLabel; ?></span>
                </a>

                <?php if ($role === 'admin' || $role === 'profissional'): ?>
                    <a href="<?php echo base_url('servicos'); ?>" class="sidebar-link <?php echo $servicosActive ? 'is-active' : ''; ?>">
                        <i class="bi bi-scissors"></i>
                        <span>Serviços</span>
                    </a>
                    <a href="<?php echo base_url('clientes'); ?>" class="sidebar-link <?php echo $clientesActive ? 'is-active' : ''; ?>">
                        <i class="bi bi-people-fill"></i>
                        <span>Clientes</span>
                    </a>
                <?php endif; ?>

                <?php if ($role === 'admin'): ?>
                    <a href="<?php echo base_url('profissionais'); ?>" class="sidebar-link <?php echo $profActive ? 'is-active' : ''; ?>">
                        <i class="bi bi-person-gear"></i>
                        <span>Profissionais</span>
                    </a>
                <?php endif; ?>

                <?php if ($role === 'admin' || $role === 'profissional'): ?>
                    <a href="<?php echo base_url('estabelecimento'); ?>" class="sidebar-link <?php echo $estabActive ? 'is-active' : ''; ?>">
                        <i class="bi bi-building"></i>
                        <span>Meu Salão</span>
                    </a>
                <?php endif; ?>

                <div class="sidebar-divider"></div>

                <a href="<?php echo base_url('configuracoes'); ?>" class="sidebar-link <?php echo $configActive ? 'is-active' : ''; ?>">
                    <i class="bi bi-gear-fill"></i>
                    <span>Configurações</span>
                </a>

                <a href="<?php echo base_url('logout'); ?>" class="sidebar-link sidebar-link--logout">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Sair</span>
                </a>
            </nav>

            <div class="sidebar-help">
                <div class="sidebar-help__icon">
                    <i class="bi bi-headset"></i>
                </div>
                <div class="sidebar-help__text">
                    <span class="sidebar-help__label">Precisa de ajuda?</span>
                    <a href="mailto:contato@glowagenda.com" class="sidebar-help__link">Fale com o suporte</a>
                </div>
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper" class="w-100">
            
            <nav class="navbar glow-topbar">
                <div class="d-flex align-items-center">
                    <i class="bi bi-list fs-3 glow-topbar__menu" id="menu-toggle" role="button"></i>
                    <h5 class="mb-0 fw-semibold glow-topbar__title"><?php echo isset($pagina) ? $pagina : 'Glow Agenda'; ?></h5>
                </div>

                <div class="ms-auto d-flex align-items-center">
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle glow-topbar__user" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle fs-4 me-1 align-middle"></i>
                            <?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário'); ?>
                            <small class="ms-1 text-muted"><?php echo htmlspecialchars($_SESSION['usuario_perfil'] ?? ''); ?></small>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end glow-dropdown" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="<?php echo base_url('configuracoes'); ?>"><i class="bi bi-person me-2"></i> Meu Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo base_url('logout'); ?>"><i class="bi bi-box-arrow-right me-2"></i> Sair</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="container-fluid glow-content">
