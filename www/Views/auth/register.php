<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastre-se - Glow Agenda</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/assets/css/style.css'); ?>">
</head>
<body class="bg-light d-flex align-items-center py-4" style="min-height: 100vh;">

    <main class="form-signin w-100 m-auto" style="max-width: 440px;">
        <div class="card shadow-sm border-0 p-4 rounded-4">
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="bi bi-stars text-primary mb-3" style="font-size: 3rem;"></i>
                    <h1 class="h3 fw-bold">Crie sua conta</h1>
                    <p class="text-muted mb-0">Cadastre-se para agendar e gerenciar seus atendimentos.</p>
                </div>

                <?php if (!empty($erros)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 small">
                            <?php foreach ($erros as $erro): ?>
                                <li><?php echo htmlspecialchars($erro); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($debug)): ?>
                    <div class="alert alert-warning">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-bug me-2"></i>
                            <strong>Detalhes para suporte</strong>
                        </div>
                        <pre class="small mb-0 bg-light border rounded p-2" style="white-space: pre-wrap; word-break: break-word;">
<?php echo htmlspecialchars(implode("\n", $debug)); ?>
                        </pre>
                    </div>
                <?php endif; ?>

                <form action="<?php echo base_url('cadastro/salvar'); ?>" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nome completo</label>
                        <input type="text" name="nome" class="form-control rounded-3" placeholder="Ex.: Maria Silva"
                               value="<?php echo htmlspecialchars($dados['nome'] ?? ''); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">E-mail</label>
                        <input type="email" name="email" class="form-control rounded-3" placeholder="voce@email.com"
                               value="<?php echo htmlspecialchars($dados['email'] ?? ''); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Telefone</label>
                        <input type="tel" name="telefone" class="form-control rounded-3" placeholder="(00) 00000-0000"
                               value="<?php echo htmlspecialchars($dados['telefone'] ?? ''); ?>">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Senha</label>
                            <input type="password" name="senha" class="form-control rounded-3" placeholder="Mínimo 6 caracteres" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Confirmar senha</label>
                            <input type="password" name="confirmar" class="form-control rounded-3" placeholder="Repita a senha" required>
                        </div>
                    </div>

                    <button class="w-100 btn btn-lg btn-primary rounded-pill fw-medium" type="submit">
                        <i class="bi bi-person-plus me-1"></i> Criar conta gratuita
                    </button>
                </form>

                <div class="text-center mt-3">
                    <span class="text-muted small">Já tem uma conta?</span>
                    <a href="<?php echo base_url('login'); ?>" class="small fw-semibold">Faça login</a>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
