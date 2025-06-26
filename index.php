<?php
// Página inicial do site
$titulo_pagina = 'Início';
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/db.php';

// Obter serviços do banco de dados
$db = Database::getInstance();
$servicos = $db->fetchAll("SELECT * FROM servicos WHERE ativo = 1 ORDER BY nome");

// Incluir cabeçalho
require_once 'includes/header.php';
?>

    <!-- Banner Principal -->
    <section class="hero-banner">
        <div class="hero-content">
            <h1>Cuidamos do que é importante para você</h1>
            <p>Soluções completas em monitoramento, segurança e automação para sua casa ou empresa</p>
            <a href="orcamento.php" class="btn">Solicitar Orçamento</a>
        </div>
    </section>

    <!-- Serviços -->
    <section class="services-section" id="servicos">
        <div class="container">
            <div class="section-title">
                <h2>Nossos Serviços</h2>
                <p>Oferecemos soluções completas em tecnologia e segurança para sua casa ou empresa</p>
            </div>
            <div class="services-grid">
                <?php foreach ($servicos as $servico): ?>
                <!-- Card Serviço -->
                <div class="service-card">
                    <div class="service-image">
                        <img src="<?php echo SITE_URL; ?>/images/servico-<?php echo $servico['slug']; ?>.jpg" alt="<?php echo sanitizar($servico['nome']); ?>">
                    </div>
                    <div class="service-content">
                        <h3><?php echo sanitizar($servico['nome']); ?></h3>
                        <p><?php echo sanitizar($servico['descricao']); ?></p>
                        <a href="servico.php?slug=<?php echo $servico['slug']; ?>" class="btn btn-sm">Saiba Mais</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Destaques -->
    <section class="features-section">
        <div class="container">
            <div class="section-title">
                <h2>Por que escolher a IDEA Service?</h2>
                <p>Conheça os diferenciais que fazem da IDEA Service a melhor escolha para sua segurança e tecnologia</p>
            </div>
            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Segurança Garantida</h3>
                    <p>Sistemas de segurança de última geração para proteger o que é mais importante para você.</p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h3>Técnicos Especializados</h3>
                    <p>Equipe técnica altamente qualificada e em constante atualização.</p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3>Suporte</h3>
                    <p>Assistência técnica disponivel todos os dias uteis.</p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h3>Garantia de Qualidade</h3>
                    <p>Todos os nossos serviços e produtos possuem garantia de qualidade.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Chamada para Ação -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Pronto para aumentar sua segurança?</h2>
                <p>Entre em contato conosco hoje mesmo e solicite um orçamento sem compromisso.</p>
                <a href="orcamento.php" class="btn btn-white">Solicitar Orçamento</a>
            </div>
        </div>
    </section>

<?php
// Incluir rodapé
require_once 'includes/footer.php';
?>
