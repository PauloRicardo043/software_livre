<?php
// Arquivo de rodapé para todas as páginas do site
?>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <img src="<?php echo SITE_URL; ?>/images/logo.png" alt="IDEA Service">
                    <p>Soluções completas em tecnologia e segurança para sua casa ou empresa.</p>
                </div>
                <div class="footer-links">
                    <h3>Links Rápidos</h3>
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>/index.php">Início</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/sobre.php">Sobre</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/servicos.php">Serviços</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/contato.php">Contato</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/orcamento.php">Orçamento</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h3>Contato</h3>
                    <p><i class="fas fa-map-marker-alt"></i> Rua Exemplo, 123 - Centro</p>
                    <p><i class="fas fa-phone"></i> (11) 99999-9999</p>
                    <p><i class="fas fa-envelope"></i> contato@ideaservices.com.br</p>
                    <p><i class="fas fa-clock"></i> Segunda - Sexta: 08:00 - 18:00</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> IDEA Service - Todos os direitos reservados</p>
            </div>
        </div>
    </footer>

    <!-- Botão WhatsApp -->
    <a href="#" class="whatsapp-float">
        <i class="fab fa-whatsapp"></i>
    </a>

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
        });
    </script>
    
    <?php if (isset($js_adicional) && is_array($js_adicional)): ?>
        <?php foreach ($js_adicional as $js): ?>
            <script src="<?php echo SITE_URL; ?>/js/<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
