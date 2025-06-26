<?php
// Página de orçamentos pendentes
$titulo_pagina = 'Orçamentos Pendentes';
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

// Verificar se o usuário está logado
iniciarSessao();
verificarLogin();

// Obter orçamentos pendentes
$db = Database::getInstance();
$orcamentos = $db->fetchAll("SELECT * FROM orcamentos WHERE status = 'pendente' ORDER BY data_solicitacao DESC");

// CSS adicional
$css_adicional = ['admin.css'];

// Incluir cabeçalho administrativo
require_once '../includes/admin_header.php';
?>

    <!-- Conteúdo da Página -->
    <div class="admin-content">
        <div class="admin-header">
            <h2>Orçamentos Pendentes</h2>
            <p>Gerencie os orçamentos que ainda não foram respondidos</p>
        </div>
        
        <?php if (empty($orcamentos)): ?>
            <div class="empty-state">
                <i class="fas fa-clipboard-list"></i>
                <h3>Nenhum orçamento pendente</h3>
                <p>Não há orçamentos pendentes para responder no momento.</p>
            </div>
        <?php else: ?>
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Serviços</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orcamentos as $orcamento): ?>
                            <tr>
                                <td>#<?php echo $orcamento['id']; ?></td>
                                <td><?php echo sanitizar($orcamento['nome']); ?></td>
                                <td><?php echo sanitizar($orcamento['email']); ?></td>
                                <td><?php echo sanitizar($orcamento['telefone']); ?></td>
                                <td>
                                    <?php 
                                    $servicos = json_decode($orcamento['servicos'] ?? '[]', true);
                                    if (is_array($servicos)) {
                                        echo implode(', ', $servicos);
                                    } else {
                                        echo sanitizar($orcamento['servicos']);
                                    }
                                    ?>
                                </td>
                                <td><?php echo formatarData($orcamento['data_solicitacao']); ?></td>
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
            </div>
        <?php endif; ?>
    </div>

<?php
// Incluir rodapé administrativo
require_once '../includes/admin_footer.php';
?>
