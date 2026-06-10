                <?php if (!empty($exibirEstabelecimentoFooter)): ?>
                    <?php
                        $footerEstabelecimento = $estabelecimentoInfo ?? [];
                        $footerNome = $footerEstabelecimento['nome_fantasia'] ?? $footerEstabelecimento['nome'] ?? 'Glow Agenda';
                        $footerEndereco = $footerEstabelecimento['endereco'] ?? '';
                        $footerCep = $footerEstabelecimento['cep'] ?? '';
                        $footerTelefone = $footerEstabelecimento['telefone'] ?? '';
                        $footerCnpj = $footerEstabelecimento['cnpj'] ?? '';
                        $footerInstagram = $footerEstabelecimento['instagram'] ?? '';
                        $footerFacebook = $footerEstabelecimento['facebook'] ?? '';
                        $footerMaps = $footerEstabelecimento['localizacao_url'] ?? '';
                    ?>
                    <footer class="app-footer border-top pt-3 mt-4 text-muted small">
                        <div class="row gy-2 align-items-center">
                            <div class="col-12 col-lg-6">
                                <div class="fw-semibold text-dark"><?php echo htmlspecialchars($footerNome); ?></div>
                                <?php if ($footerEndereco): ?>
                                    <div class="d-flex align-items-start gap-2 mt-2">
                                        <i class="bi bi-geo-alt"></i>
                                        <div>
                                            <?php echo htmlspecialchars($footerEndereco); ?>
                                            <?php if ($footerCep): ?><div>CEP: <?php echo htmlspecialchars($footerCep); ?></div><?php endif; ?>
                                            <?php if ($footerMaps): ?><a class="link-primary" href="<?php echo htmlspecialchars($footerMaps); ?>" target="_blank" rel="noopener noreferrer">Ver no mapa</a><?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-12 col-lg-6 text-lg-end">
                                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-lg-end gap-3">
                                    <?php if ($footerTelefone): ?>
                                        <span>
                                            <i class="bi bi-telephone me-1"></i>
                                            <a class="link-dark" href="tel:<?php echo preg_replace('/\D+/', '', $footerTelefone); ?>"><?php echo htmlspecialchars($footerTelefone); ?></a>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($footerCnpj): ?>
                                        <span><i class="bi bi-file-earmark-text me-1"></i>CNPJ: <?php echo htmlspecialchars($footerCnpj); ?></span>
                                    <?php endif; ?>
                                    <span class="d-flex align-items-center gap-2">
                                        <?php if ($footerInstagram): ?><a class="text-secondary" href="<?php echo htmlspecialchars($footerInstagram); ?>" target="_blank" rel="noopener noreferrer"><i class="bi bi-instagram"></i></a><?php endif; ?>
                                        <?php if ($footerFacebook): ?><a class="text-secondary" href="<?php echo htmlspecialchars($footerFacebook); ?>" target="_blank" rel="noopener noreferrer"><i class="bi bi-facebook"></i></a><?php endif; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </footer>
                <?php endif; ?>
            </div> <!-- Fechamento da div container-fluid aberta em sidebar.php -->
        </div> <!-- Fechamento da div page-content-wrapper aberta em sidebar.php -->
    </div> <!-- Fechamento da div wrapper aberta em header.php -->

    <script>
        // Script para o Toggle Menu (Mobile e Desktop)
        document.addEventListener("DOMContentLoaded", function() {
            var toggleBtn = document.getElementById("menu-toggle");
            if(toggleBtn) {
                toggleBtn.addEventListener("click", function(e) {
                    e.preventDefault();
                    document.getElementById("wrapper").classList.toggle("toggled");
                });
            }
        });
    </script>
