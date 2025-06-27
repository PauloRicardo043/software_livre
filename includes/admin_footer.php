<?php
// Rodapé para páginas administrativas
?>
        </div> <!-- Fechamento do .admin-container que começa no admin_header.php -->
    </div> <!-- Fechamento do .admin-content que começa nas páginas individuais -->

    <!-- Scripts -->
    <script>
        // Menu mobile
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.querySelector('.menu-toggle');
            const nav = document.querySelector('nav');
            
            if (menuToggle && nav) {
                menuToggle.addEventListener('click', function() {
                    nav.classList.toggle('active');
                    menuToggle.classList.toggle('active');
                });
            }
            
            // Fechar mensagem flash
            const mensagemFlash = document.querySelector('.mensagem-flash');
            const fecharMensagem = document.querySelector('.fechar-mensagem');
            
            if (mensagemFlash && fecharMensagem) {
                fecharMensagem.addEventListener('click', function() {
                    mensagemFlash.style.display = 'none';
                });
                
                // Auto-fechar após 5 segundos
                setTimeout(function() {
                    mensagemFlash.style.display = 'none';
                }, 5000);
            }
            
            // Confirmação para exclusão
            const deleteButtons = document.querySelectorAll('.btn-delete');
            if (deleteButtons) {
                deleteButtons.forEach(function(button) {
                    button.addEventListener('click', function(e) {
                        if (!confirm('Tem certeza que deseja excluir este item?')) {
                            e.preventDefault();
                        }
                    });
                });
            }
        });
    </script>
    
    <?php if (isset($js_adicional) && is_array($js_adicional)): ?>
        <?php foreach ($js_adicional as $js): ?>
            <script src="<?php echo SITE_URL; ?>/js/<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
