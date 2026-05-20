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
