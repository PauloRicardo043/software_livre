<?php
// Página de orçamentos expirados
$titulo_pagina = 'Orçamentos Expirados';
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

// Verificar se o usuário está logado
iniciarSessao();
verificarLogin();

// Obter orçamentos expirados do banco de dados
$db = Database::getInstance();
$orcamentos = $db->fetchAll("
    SELECT o.*, 
           GROUP_CONCAT(s.nome SEPARATOR ', ') as servicos_nomes
    FROM orcamentos o
    LEFT JOIN orcamentos_servicos os ON o.id = os.orcamento_id
    LEFT JOIN servicos s ON os.servico_id = s.id
    WHERE o.status = 'expirado'
    GROUP BY o.id
    ORDER BY o.data_expiracao DESC
");

// CSS adicional
$css_adicional = ['admin.css'];

// Incluir cabeçalho administrativo
require_once '../includes/admin_header.php';
?>

    <!-- Conteúdo Principal -->
    <div class="admin-content">
        <div class="admin-header">
            <h2>Orçamentos Expirados</h2>
            <p>Visualize os orçamentos que expiraram sem resposta</p>
        </div>
        
        <div class="admin-actions">
            <div class="admin-search">
                <input type="text" id="searchInput" placeholder="Buscar orçamento...">
                <button type="button"><i class="fas fa-search"></i></button>
            </div>
        </div>
        
        <div class="admin-table-container">
            <?php if (empty($orcamentos)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>Nenhum orçamento expirado</h3>
                    <p>Não há orçamentos expirados no momento.</p>
                </div>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Serviços</th>
                            <th>Data Solicitação</th>
                            <th>Data Expiração</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orcamentos as $orcamento): ?>
                            <tr>
                                <td>#<?php echo $orcamento['id']; ?></td>
                                <td>
                                    <strong><?php echo sanitizar($orcamento['nome']); ?></strong><br>
                                    <small><?php echo sanitizar($orcamento['email']); ?></small>
                                </td>
                                <td><?php echo sanitizar($orcamento['servicos_nomes']); ?></td>
                                <td><?php echo formatarData($orcamento['data_solicitacao']); ?></td>
                                <td><?php echo formatarData($orcamento['data_expiracao']); ?></td>
                                <td class="actions">
                                    <a href="orcamento-visualizar.php?id=<?php echo $orcamento['id']; ?>" class="btn-icon" title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="orcamento-responder.php?id=<?php echo $orcamento['id']; ?>" class="btn-icon" title="Responder">
                                        <i class="fas fa-reply"></i>
                                    </a>
                                    <a href="orcamento-excluir.php?id=<?php echo $orcamento['id']; ?>" class="btn-icon btn-delete" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

<?php
// Incluir rodapé administrativo
require_once '../includes/admin_footer.php';
?>
