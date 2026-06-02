<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Esqueci minha senha</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center py-4" style="min-height: 100vh;">
  <main class="w-100 m-auto" style="max-width: 420px;">
    <div class="card shadow-sm border-0 p-4 rounded-4">
      <div class="card-body">
        <h1 class="h4 mb-3">Recuperar senha</h1>
        <?php if (!empty($msg)): ?>
          <div class="alert alert-success py-2 small"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>
        <?php if (!empty($err)): ?>
          <div class="alert alert-danger py-2 small"><?php echo htmlspecialchars($err); ?></div>
        <?php endif; ?>
        <form method="POST" action="<?php echo base_url('auth/send-reset'); ?>">
          <div class="mb-3">
            <label class="form-label">E-mail</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit">Enviar link</button>
            <a class="btn btn-outline-secondary" href="<?php echo base_url('login'); ?>">Voltar ao login</a>
          </div>
        </form>
      </div>
    </div>
  </main>
</body>
</html>
