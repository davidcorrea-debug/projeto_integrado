<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Glow Agenda</title>
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
    
    <main class="form-signin w-100 m-auto" style="max-width: 400px;">
        <div class="card shadow-sm border-0 p-4 rounded-4">
            <div class="card-body text-center">
                <i class="bi bi-stars text-primary mb-3" style="font-size: 3rem;"></i>
                <h1 class="h3 mb-4 fw-bold">Glow Agenda</h1>
                <p class="text-muted mb-4">Faça login para gerenciar seu salão.</p>
                
                <form action="<?php echo base_url('auth/authenticate'); ?>" method="POST">
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
                    <p class="mt-4 mb-3 text-muted">&copy; 2026 — Glow Agenda</p>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
