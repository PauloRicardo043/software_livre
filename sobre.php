<?php
// Página sobre a empresa
$titulo_pagina = 'Sobre a IDEA Service';
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/db.php';

// Incluir cabeçalho
require_once 'includes/header.php';
?>

    <!-- Banner da Página -->
    <section class="hero-banner">
        <div class="hero-content">
            <h1>Sobre a IDEA Service</h1>
            <p>Conheça nossa história e nossos valores</p>
        </div>
    </section>

    <!-- História da Empresa -->
    <section class="about-section">
        <div class="container">
            <div class="about-content">
                <div class="about-image">
                    <img src="images/banner.png" alt="IDEA Service - Nossa História">
                </div>
                <div class="about-text">
                    <h2>Nossa História</h2>
                    <p>Fundada em 2025, a IDEA Service nasceu da visão de oferecer soluções tecnológicas integradas com foco em segurança e automação. Nossa jornada começou com um pequeno escritório e uma equipe de técnicos especializados, determinados a transformar o mercado de serviços tecnológicos.</p>
                    
                    <p>Ao longo dos anos, expandimos nosso portfólio de serviços e construímos uma reputação sólida baseada na excelência técnica e no atendimento personalizado. Hoje, somos reconhecidos como referência em sistemas de monitoramento, instalações elétricas, automação residencial e industrial, sistemas de alarme e portões eletrônicos.</p>
                    
                    <p>Nossa missão é proporcionar tranquilidade e segurança aos nossos clientes através de soluções tecnológicas confiáveis e inovadoras. Trabalhamos incansavelmente para garantir que cada projeto seja executado com o mais alto padrão de qualidade, utilizando equipamentos de ponta e técnicas avançadas.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Valores da Empresa -->
    <section class="values-section">
        <div class="container">
            <div class="section-title">
                <h2>Nossos Valores</h2>
                <p>Princípios que guiam nosso trabalho diariamente</p>
            </div>
            
            <div class="values-grid">
                <div class="value-item">
                    <div class="value-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Segurança</h3>
                    <p>Priorizamos a segurança em todos os aspectos do nosso trabalho, desde a seleção de equipamentos até a implementação de soluções.</p>
                </div>
                
                <div class="value-item">
                    <div class="value-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <h3>Excelência Técnica</h3>
                    <p>Investimos continuamente na capacitação da nossa equipe e na aquisição de tecnologias de ponta para oferecer o melhor serviço.</p>
                </div>
                
                <div class="value-item">
                    <div class="value-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>Confiabilidade</h3>
                    <p>Cumprimos rigorosamente nossos prazos e compromissos, construindo relações de confiança duradouras com nossos clientes.</p>
                </div>
                
                <div class="value-item">
                    <div class="value-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h3>Inovação</h3>
                    <p>Buscamos constantemente soluções inovadoras que possam agregar valor e eficiência aos projetos dos nossos clientes.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Equipe -->
    <section class="team-section">
        <div class="container">
            <div class="section-title">
                <h2>Nossa Equipe</h2>
                <p>Profissionais especializados e comprometidos com a excelência</p>
            </div>
            
            <div class="team-description">
                <p>A IDEA Service conta com uma equipe de técnicos altamente qualificados e especializados em diversas áreas da tecnologia. Nossos profissionais possuem certificações nas principais tecnologias do mercado e passam por treinamentos constantes para se manterem atualizados com as últimas tendências e inovações.</p>
                
                <p>Cada membro da nossa equipe é selecionado não apenas por suas habilidades técnicas, mas também por sua capacidade de entender as necessidades específicas de cada cliente e oferecer soluções personalizadas. Acreditamos que o diferencial da IDEA Service está na combinação de conhecimento técnico avançado com um atendimento humanizado e focado no cliente.</p>
            </div>
            
            <div class="team-highlights">
                <div class="highlight-item">
                    <div class="highlight-number">15+</div>
                    <div class="highlight-text">Técnicos Especializados</div>
                </div>
                
                <div class="highlight-item">
                    <div class="highlight-number">30+</div>
                    <div class="highlight-text">Certificações Técnicas</div>
                </div>
                
                <div class="highlight-item">
                    <div class="highlight-number">100+</div>
                    <div class="highlight-text">Projetos Concluídos</div>
                </div>
                
                <div class="highlight-item">
                    <div class="highlight-number">98%</div>
                    <div class="highlight-text">Clientes Satisfeitos</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Compromisso com o Cliente -->
    <section class="commitment-section">
        <div class="container">
            <div class="commitment-content">
                <div class="commitment-text">
                    <h2>Nosso Compromisso</h2>
                    <p>Na IDEA Service, nosso compromisso vai além da simples prestação de serviços. Buscamos construir parcerias duradouras com nossos clientes, entendendo suas necessidades específicas e oferecendo soluções que realmente façam a diferença em seus negócios ou residências.</p>
                    
                    <p>Garantimos a qualidade de todos os nossos serviços e oferecemos suporte contínuo após a conclusão dos projetos. Estamos sempre disponíveis para esclarecer dúvidas, realizar manutenções preventivas e corretivas, e ajudar nossos clientes a tirar o máximo proveito das soluções implementadas.</p>
                    
                    <a href="contato.php" class="btn">Entre em Contato</a>
                </div>
                <div class="commitment-image">
                    <img src="images/compromisso.jpg" alt="Nosso Compromisso com o Cliente">
                </div>
            </div>
        </div>
    </section>

<?php
// Incluir rodapé
require_once 'includes/footer.php';
?>
