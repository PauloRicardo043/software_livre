<?php
// Página de detalhes do serviço
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/db.php';

// Verificar se o slug foi fornecido
if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    // Redirecionar para a página de serviços se não houver slug
    header('Location: servicos.php');
    exit;
}

$slug = $_GET['slug'];

// Obter detalhes do serviço do banco de dados
$db = Database::getInstance();
$servico = $db->fetchOne("SELECT * FROM servicos WHERE slug = ? AND ativo = 1", [$slug]);

// Se o serviço não existir, redirecionar para a página de serviços
if (!$servico) {
    setMensagem('error', 'Serviço não encontrado.');
    header('Location: servicos.php');
    exit;
}

$titulo_pagina = $servico['nome'];

// Incluir cabeçalho
require_once 'includes/header.php';
?>

    <!-- Detalhe do Serviço -->
    <section class="service-detail">
        <div class="container">
            <div class="service-detail-container">
                <div class="service-detail-img">
                    <img src="<?php echo SITE_URL; ?>/images/servico-<?php echo $servico['slug']; ?>.jpg" alt="<?php echo sanitizar($servico['nome']); ?>">
                </div>
                <div class="service-detail-content">
                    <h2><?php echo sanitizar($servico['nome']); ?></h2>
                    <p><?php echo nl2br(sanitizar($servico['descricao'])); ?></p>
                    
                    <?php
                    // Conteúdo específico para cada tipo de serviço
                    switch ($servico['slug']):
                        case 'sistemas-monitoramento':
                    ?>
                        <h3>O que oferecemos:</h3>
                        <ul>
                            <li>Câmeras de segurança HD, Full HD e 4K</li>
                            <li>Sistemas de monitoramento com visão noturna</li>
                            <li>Acesso remoto via smartphone ou computador</li>
                            <li>Gravação em nuvem ou DVR/NVR</li>
                            <li>Instalação profissional e certificada</li>
                            <li>Manutenção preventiva e corretiva</li>
                            <li>Suporte técnico especializado</li>
                        </ul>
                        
                        <h3>Benefícios:</h3>
                        <ul>
                            <li>Maior segurança para sua família ou negócio</li>
                            <li>Monitoramento em tempo real de qualquer lugar</li>
                            <li>Registro de eventos para evidências</li>
                            <li>Efeito preventivo contra invasões e furtos</li>
                            <li>Tranquilidade mesmo quando estiver ausente</li>
                        </ul>
                    <?php
                        break;
                        case 'instalacoes-eletricas':
                    ?>
                        <h3>O que oferecemos:</h3>
                        <ul>
                            <li>Instalação elétrica completa para residências</li>
                            <li>Manutenção preventiva e corretiva</li>
                            <li>Troca de quadros de distribuição</li>
                            <li>Instalação de tomadas, interruptores e pontos de luz</li>
                            <li>Instalação de disjuntores e dispositivos de proteção</li>
                            <li>Adequação às normas técnicas vigentes</li>
                            <li>Projetos elétricos personalizados</li>
                        </ul>
                        
                        <h3>Benefícios:</h3>
                        <ul>
                            <li>Maior segurança para sua família</li>
                            <li>Prevenção de curtos-circuitos e incêndios</li>
                            <li>Economia de energia elétrica</li>
                            <li>Instalações duráveis e de qualidade</li>
                            <li>Conformidade com normas técnicas</li>
                        </ul>
                    <?php
                        break;
                        case 'automacao':
                    ?>
                        <h3>O que oferecemos:</h3>
                        <ul>
                            <li>Automação residencial completa</li>
                            <li>Controle de iluminação</li>
                            <li>Controle de temperatura e climatização</li>
                            <li>Automação de cortinas e persianas</li>
                            <li>Sistemas de áudio e vídeo integrados</li>
                            <li>Controle de acesso</li>
                            <li>Automação industrial</li>
                        </ul>
                        
                        <h3>Benefícios:</h3>
                        <ul>
                            <li>Maior conforto e praticidade no dia a dia</li>
                            <li>Economia de energia</li>
                            <li>Aumento da segurança</li>
                            <li>Valorização do imóvel</li>
                            <li>Controle centralizado de todos os sistemas</li>
                        </ul>
                    <?php
                        break;
                        case 'alarmes':
                    ?>
                        <h3>O que oferecemos:</h3>
                        <ul>
                            <li>Instalação de sistemas de alarme residenciais e comerciais</li>
                            <li>Sensores de movimento de alta precisão</li>
                            <li>Sensores de abertura para portas e janelas</li>
                            <li>Centrais de alarme com tecnologia avançada</li>
                            <li>Sistemas com monitoramento 24 horas</li>
                            <li>Alarmes com notificação via aplicativo</li>
                            <li>Manutenção preventiva e corretiva</li>
                        </ul>
                        
                        <h3>Benefícios:</h3>
                        <ul>
                            <li>Maior segurança para sua residência ou empresa</li>
                            <li>Resposta rápida em caso de invasão</li>
                            <li>Efeito preventivo contra furtos e roubos</li>
                            <li>Monitoramento constante do seu patrimônio</li>
                            <li>Tranquilidade mesmo quando estiver ausente</li>
                        </ul>
                    <?php
                        break;
                        case 'portoes-eletronicos':
                    ?>
                        <h3>O que oferecemos:</h3>
                        <ul>
                            <li>Instalação de motores para portões deslizantes</li>
                            <li>Instalação de motores para portões basculantes</li>
                            <li>Instalação de motores para portões pivotantes</li>
                            <li>Manutenção preventiva e corretiva</li>
                            <li>Troca de componentes eletrônicos</li>
                            <li>Instalação de controles remotos e receptores</li>
                            <li>Sistemas de automação para portões</li>
                        </ul>
                        
                        <h3>Benefícios:</h3>
                        <ul>
                            <li>Maior praticidade no dia a dia</li>
                            <li>Aumento da segurança da sua residência ou empresa</li>
                            <li>Valorização do imóvel</li>
                            <li>Equipamentos de alta durabilidade</li>
                            <li>Garantia em todos os serviços realizados</li>
                        </ul>
                    <?php
                        break;
                        default:
                    ?>
                        <p>Para mais informações sobre este serviço, entre em contato conosco.</p>
                    <?php endswitch; ?>
                    
                    <a href="orcamento.php?servico=<?php echo $servico['id']; ?>" class="btn">Solicitar Orçamento</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Chamada para Ação -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Precisa de um serviço personalizado?</h2>
                <p>Entre em contato conosco hoje mesmo e solicite um orçamento sem compromisso.</p>
                <a href="orcamento.php" class="btn btn-white">Solicitar Orçamento</a>
            </div>
        </div>
    </section>

<?php
// Incluir rodapé
require_once 'includes/footer.php';
?>
