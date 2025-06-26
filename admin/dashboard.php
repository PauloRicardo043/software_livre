<?php
// Dashboard do administrador
$titulo_pagina = 'Dashboard Administrativo';
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

// Verificar se o usuário está logado
iniciarSessao();
verificarLogin();

// Obter dados para os cards
$db = Database::getInstance();

// Contagem de orçamentos por status
$orcamentos_pendentes = $db->fetchOne("SELECT COUNT(*) as total FROM orcamentos WHERE status = 'pendente'")['total'];
$orcamentos_respondidos = $db->fetchOne("SELECT COUNT(*) as total FROM orcamentos WHERE status = 'respondido'")['total'];
$orcamentos_aprovados = $db->fetchOne("SELECT COUNT(*) as total FROM orcamentos WHERE status = 'aprovado'")['total'];
$orcamentos_recusados = $db->fetchOne("SELECT COUNT(*) as total FROM orcamentos WHERE status = 'recusado'")['total'];
$orcamentos_expirados = $db->fetchOne("SELECT COUNT(*) as total FROM orcamentos WHERE status = 'expirado'")['total'];

// Contagem de contatos por status
$contatos_recebidos = $db->fetchOne("SELECT COUNT(*) as total FROM contatos WHERE status = 'novo'")['total'];
$contatos_respondidos = $db->fetchOne("SELECT COUNT(*) as total FROM contatos WHERE status = 'respondido'")['total'];

// CSS adicional
$css_adicional = ['admin.css'];

// Incluir cabeçalho administrativo
require_once '../includes/admin_header.php';
?>

    <!-- Dashboard -->
    <div class="admin-content">
        <div class="admin-header">
            <h2>Dashboard</h2>
            <p>Bem-vindo, <?php echo sanitizar($_SESSION['usuario_nome']); ?>!</p>
        </div>
        
        <div class="dashboard-cards">
            <!-- Card Orçamentos Pendentes -->
            <div class="dashboard-card">
                <div class="card-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="card-content">
                    <h3>Orçamentos Pendentes</h3>
                    <p class="card-number"><?php echo $orcamentos_pendentes; ?></p>
                    <a href="orcamentos-pendentes.php" class="card-link">Ver Detalhes</a>
                </div>
            </div>
            
            <!-- Card Orçamentos Respondidos -->
            <div class="dashboard-card">
                <div class="card-icon completed">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <div class="card-content">
                    <h3>Orçamentos Respondidos</h3>
                    <p class="card-number"><?php echo $orcamentos_respondidos; ?></p>
                    <a href="orcamentos-respondidos.php" class="card-link">Ver Detalhes</a>
                </div>
            </div>
            
            <!-- Card Orçamentos Aprovados -->
            <div class="dashboard-card">
                <div class="card-icon approved">
                    <i class="fas fa-circle-check"></i>
                </div>
                <div class="card-content">
                    <h3>Orçamentos Aprovados</h3>
                    <p class="card-number"><?php echo $orcamentos_aprovados; ?></p>
                    <a href="orcamentos-aprovados.php" class="card-link">Ver Detalhes</a>
                </div>
            </div>
            
            <!-- Card Orçamentos Recusados -->
            <div class="dashboard-card">
                <div class="card-icon rejected">
                    <i class="fas fa-circle-xmark"></i>
                </div>
                <div class="card-content">
                    <h3>Orçamentos Recusados</h3>
                    <p class="card-number"><?php echo $orcamentos_recusados; ?></p>
                    <a href="orcamentos-recusados.php" class="card-link">Ver Detalhes</a>
                </div>
            </div>
            
            <!-- Card Orçamentos Expirados -->
            <div class="dashboard-card">
                <div class="card-icon expired">
                    <i class="fas fa-calendar-xmark"></i>
                </div>
                <div class="card-content">
                    <h3>Orçamentos Expirados</h3>
                    <p class="card-number"><?php echo $orcamentos_expirados; ?></p>
                    <a href="orcamentos-expirados.php" class="card-link">Ver Detalhes</a>
                </div>
            </div>
            
            <!-- Card Contatos Recebidos -->
            <div class="dashboard-card">
                <div class="card-icon contact-new">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
                <div class="card-content">
                    <h3>Contatos Recebidos</h3>
                    <p class="card-number"><?php echo $contatos_recebidos; ?></p>
                    <a href="contatos.php?status=novo" class="card-link">Ver Detalhes</a>
                </div>
            </div>
            
            <!-- Card Contatos Respondidos -->
            <div class="dashboard-card">
                <div class="card-icon contact-replied">
                    <i class="fas fa-reply-all"></i>
                </div>
                <div class="card-content">
                    <h3>Contatos Respondidos</h3>
                    <p class="card-number"><?php echo $contatos_respondidos; ?></p>
                    <a href="contatos.php?status=respondido" class="card-link">Ver Detalhes</a>
                </div>
            </div>
            
            <!-- Card Configurações -->
            <div class="dashboard-card">
                <div class="card-icon settings">
                    <i class="fas fa-cog"></i>
                </div>
                <div class="card-content">
                    <h3>Configurações</h3>
                    <p>Gerenciar conta e sistema</p>
                    <a href="configuracoes.php" class="card-link">Acessar</a>
                </div>
            </div>
            
            <!-- Card Clientes -->
            <div class="dashboard-card">
                <div class="card-icon clients">
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-content">
                    <h3>Clientes</h3>
                    <p>Gerenciar cadastros</p>
                    <a href="clientes.php" class="card-link">Acessar</a>
                </div>
            </div>
        </div>
    </div>

<?php
// Incluir rodapé administrativo
require_once '../includes/admin_footer.php';
?>
