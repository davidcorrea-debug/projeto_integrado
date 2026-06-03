<?php
if (!empty($msg)) echo $msg;
$old = $_SESSION['form_profissional'] ?? [];
$detalhesErro = $_SESSION['form_profissional_erro']['mensagens'] ?? [];
unset($_SESSION['form_profissional']);
unset($_SESSION['form_profissional_erro']['mensagens']);
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-semibold"><?php echo htmlspecialchars($pagina); ?></h5>
            </div>
            <div class="card-body p-4">
                <form id="formProfissional" action="<?php echo base_url('profissionais/salvar'); ?>" method="POST">
                    <?php if (!empty($detalhesErro)): ?>
                        <div class="alert alert-warning">
                            <ul class="mb-0">
                                <?php foreach ($detalhesErro as $erroItem): ?>
                                    <li><?php echo htmlspecialchars($erroItem); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nome completo <span class="text-danger">*</span></label>
                        <input type="text" id="usuario_nome" name="usuario_nome" class="form-control" value="<?php echo htmlspecialchars($old['usuario_nome'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">E-mail <span class="text-danger">*</span></label>
                        <input type="email" id="usuario_email" name="usuario_email" class="form-control" value="<?php echo htmlspecialchars($old['usuario_email'] ?? ''); ?>" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Senha <span class="text-danger">*</span></label>
                            <input type="password" id="usuario_senha" name="usuario_senha" class="form-control" minlength="6" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Confirmar senha <span class="text-danger">*</span></label>
                            <input type="password" id="confirmar_senha" name="confirmar_senha" class="form-control" minlength="6" required>
                        </div>
                    </div>

                    <div class="alert alert-info small">
                        O perfil será definido automaticamente como <strong>profissional</strong>.
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="robot bi-check-lg me-1"></i> Cadastrar
                        </button>
                        <a href="<?php echo base_url('profissionais'); ?>" class="btn btn-outline-secondary rounded-pill px-4">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formProfissional');
    console.log('%c[PROF-FORM] Form loaded', 'color: blue; font-weight: bold;');
    console.log('Action:', form.action);
    console.log('Method:', form.method);
    console.log('Prefilled name:', document.getElementById('usuario_nome').value);
    console.log('Prefilled email:', document.getElementById('usuario_email').value);
    
    form.addEventListener('submit', function(e) {
        const nome = document.getElementById('usuario_nome').value.trim();
        const email = document.getElementById('usuario_email').value.trim();
        const senha = document.getElementById('usuario_senha').value;
        const conf = document.getElementById('confirmar_senha').value;
        
        console.log('%c[PROF-FORM] Form submission detected', 'color: orange; font-weight: bold;');
        console.log('Nome:', nome, '(len=' + nome.length + ')');
        console.log('Email:', email, '(len=' + email.length + ')');
        console.log('Senha length:', senha.length);
        console.log('Confirmar length:', conf.length);
        console.log('Passwords match:', senha === conf);
        console.log('Email valid:', /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email));
        console.log('All fields filled:', !!(nome && email && senha && conf));
        console.log('Posting to:', form.action);
        console.log('Timestamp:', new Date().toISOString());
        
        // Log FormData que será enviado
        const formData = new FormData(form);
        console.log('%c[PROF-FORM] FormData being sent:', 'color: green;');
        for (let [key, value] of formData.entries()) {
            console.log('  ' + key + ':', value.substring ? value.substring(0, 50) : value);
        }
    });
    
    // Monitora redirecionamentos
    window.addEventListener('beforeunload', function() {
        console.log('%c[PROF-FORM] Page unloading (redirect detected)', 'color: red;');
        console.log('Current URL:', window.location.href);
    });
});
</script>
