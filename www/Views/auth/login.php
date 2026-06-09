<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
        $dadosEstabelecimento = $estabelecimento ?? [];
        $nomeSalao = $dadosEstabelecimento['nome_fantasia'] ?? $dadosEstabelecimento['nome'] ?? 'Glow Agenda';
        $telefoneSalao = $dadosEstabelecimento['telefone'] ?? '';
        $enderecoSalao = $dadosEstabelecimento['endereco'] ?? '';
        $cepSalao = $dadosEstabelecimento['cep'] ?? '';
        $cnpjSalao = $dadosEstabelecimento['cnpj'] ?? '';
        $instagramSalao = $dadosEstabelecimento['instagram'] ?? '';
        $facebookSalao = $dadosEstabelecimento['facebook'] ?? '';
        $siteSalao = $dadosEstabelecimento['site'] ?? '';
        $mapsSalao = $dadosEstabelecimento['localizacao_url'] ?? '';
    ?>
    <title>Login - <?php echo htmlspecialchars($nomeSalao); ?></title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/assets/css/style.css'); ?>">
</head>
<body class="bg-light d-flex align-items-center py-4" style="min-height: 100vh;">
    
    <main class="w-100 m-auto" style="max-width: 900px;">
        <div class="row g-4 align-items-stretch">
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm border-0 p-4 rounded-4 h-100">
                    <div class="card-body d-flex flex-column justify-content-center text-center text-lg-start">
                        <?php if (!empty($dadosEstabelecimento['logo'])): ?>
                            <div class="mb-4 text-center">
                                <img src="<?php echo base_url($dadosEstabelecimento['logo']); ?>" alt="Foto do estabelecimento" class="img-fluid rounded-4 shadow-sm" style="max-height: 220px; object-fit: cover;">
                            </div>
                        <?php else: ?>
                            <i class="bi bi-stars text-primary mb-3" style="font-size: 3rem;"></i>
                        <?php endif; ?>
                        <h1 class="h3 mb-2 fw-bold">Bem-vindo(a) ao <?php echo htmlspecialchars($nomeSalao); ?></h1>
                        <p class="text-muted mb-4">Faça login para agendar horários, acompanhar seus serviços e gerenciar sua experiência no salão.</p>
                        <div class="d-flex gap-3 justify-content-center justify-content-lg-start flex-wrap">
                            <?php if ($instagramSalao): ?>
                                <a class="btn btn-outline-secondary rounded-pill px-3" href="<?php echo htmlspecialchars($instagramSalao); ?>" target="_blank" rel="noopener noreferrer">
                                    <i class="bi bi-instagram me-1"></i> Instagram
                                </a>
                            <?php endif; ?>
                            <?php if ($facebookSalao): ?>
                                <a class="btn btn-outline-secondary rounded-pill px-3" href="<?php echo htmlspecialchars($facebookSalao); ?>" target="_blank" rel="noopener noreferrer">
                                    <i class="bi bi-facebook me-1"></i> Facebook
                                </a>
                            <?php endif; ?>
                            <?php if ($siteSalao): ?>
                                <a class="btn btn-outline-secondary rounded-pill px-3" href="<?php echo htmlspecialchars($siteSalao); ?>" target="_blank" rel="noopener noreferrer">
                                    <i class="bi bi-globe me-1"></i> Site
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card shadow-sm border-0 p-4 rounded-4 h-100">
                    <div class="card-body">
                        <h2 class="h4 fw-semibold mb-4 text-center text-lg-start">Acesse sua conta</h2>
                        <form action="<?php echo base_url('auth/authenticate'); ?>" method="POST">
                    <?php if (!empty($sucesso)): ?>
                        <div class="alert alert-success py-2 small mb-3">
                            <i class="bi bi-check-circle-fill me-1"></i>
                            <?php echo htmlspecialchars($sucesso); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($erro)): ?>
                        <div class="alert alert-danger py-2 small">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            <?php echo htmlspecialchars($erro); ?>
                        </div>
                    <?php endif; ?>
                    <div class="form-floating mb-3 text-start">
                        <input type="email" class="form-control rounded-3" id="floatingInput" name="email"
                               placeholder="nome@exemplo.com" required autofocus>
                        <label for="floatingInput">E-mail</label>
                    </div>
                    <div class="form-floating mb-4 text-start">
                        <input type="password" class="form-control rounded-3" id="floatingPassword" name="senha"
                               placeholder="Senha" required>
                        <label for="floatingPassword">Senha</label>
                    </div>

                            <button class="w-100 btn btn-lg btn-primary rounded-pill fw-medium" type="submit">Entrar no sistema</button>
                            <div class="mt-3 text-center">
                                <a href="<?php echo base_url('forgot-password'); ?>" class="small">Esqueci minha senha</a>
                                <span class="text-muted mx-1">•</span>
                                <a href="<?php echo base_url('cadastro'); ?>" class="small">Criar conta</a>
                            </div>
                        </form>

                        <hr class="my-4">

                        <div class="small text-muted">
                            <div class="d-flex align-items-start mb-2">
                                <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                                <div>
                                    <strong>Endereço:</strong><br>
                                    <?php echo htmlspecialchars($enderecoSalao); ?>
                                    <?php if ($cepSalao): ?>
                                        <br><span>CEP: <?php echo htmlspecialchars($cepSalao); ?></span>
                                    <?php endif; ?>
                                    <?php if ($mapsSalao): ?>
                                        <br><a class="link-primary" href="<?php echo htmlspecialchars($mapsSalao); ?>" target="_blank" rel="noopener noreferrer">Ver no mapa</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-telephone-fill text-primary me-2"></i>
                                <div>
                                    <strong>Telefone:</strong>
                                    <?php if ($telefoneSalao): ?>
                                        <a class="ms-1" href="tel:<?php echo preg_replace('/\D+/', '', $telefoneSalao); ?>">
                                            <?php echo htmlspecialchars($telefoneSalao); ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="ms-1">(informação não disponível)</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($cnpjSalao): ?>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark-text-fill text-primary me-2"></i>
                                    <div>
                                        <strong>CNPJ:</strong>
                                        <span class="ms-1"><?php echo htmlspecialchars($cnpjSalao); ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <p class="mt-4 mb-0 text-muted text-center text-lg-start">&copy; <?php echo date('Y'); ?> — <?php echo htmlspecialchars($nomeSalao); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
