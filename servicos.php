<?php
// Página de serviços
$titulo_pagina = 'Serviços';
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/db.php';

// Obter serviços do banco de dados
$db = Database::getInstance();
$servicos = $db->fetchAll("SELECT * FROM servicos WHERE ativo = 1 ORDER BY nome");

// Incluir cabeçalho
require_once 'includes/header.php';
?>

    <!-- Banner da Página -->
    <section class="hero-banner">
        <div class="hero-content">
            <h1>Nossos Serviços</h1>
            <p>Conheça as soluções completas que a IDEA Service oferece para você</p>
        </div>
    </section>

    <!-- Serviços -->
    <section class="services-section" id="servicos">
        <div class="container">
            <div class="section-title">
                <h2>Soluções Completas</h2>
                <p>Oferecemos uma ampla gama de serviços para atender às suas necessidades de segurança e tecnologia</p>
            </div>
            <div class="services-grid">
                <?php foreach ($servicos as $servico): ?>
                <!-- Card Serviço -->
                <div class="service-card">
                    <div class="service-card">
                        <img src="<?php echo SITE_URL; ?>/images/servico-<?php echo $servico['slug']; ?>.jpg" alt="<?php echo sanitizar($servico['nome']); ?>">
                    </div>
                    <div class="service-card-content">
                        <h3><?php echo sanitizar($servico['nome']); ?></h3>
                        <p><?php echo sanitizar($servico['descricao']); ?></p>
                        <a href="servico.php?slug=<?php echo $servico['slug']; ?>" class="btn-outline">Saiba Mais</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Chamada para Ação -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Precisa de um orçamento personalizado?</h2>
                <p>Entre em contato conosco hoje mesmo e solicite um orçamento sem compromisso.</p>
                <a href="orcamento.php" class="btn btn-white">Solicitar Orçamento</a>
            </div>
        </div>
    </section>

<?php
// Incluir rodapé
require_once 'includes/footer.php';
?>
