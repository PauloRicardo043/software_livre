<?php
// Página de orçamentos respondidos
$titulo_pagina = 'Orçamentos Respondidos';
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

// Verificar se o usuário está logado
iniciarSessao();
verificarLogin();

// Obter orçamentos respondidos do banco de dados
$db = Database::getInstance();
$orcamentos = $db->fetchAll("
    SELECT o.*, 
           GROUP_CONCAT(s.nome SEPARATOR ', ') as servicos_nomes,
           u.nome as respondido_por,
           r.valor as valor_orcamento
    FROM orcamentos o
    LEFT JOIN orcamentos_servicos os ON o.id = os.orcamento_id
    LEFT JOIN servicos s ON os.servico_id = s.id
    LEFT JOIN orcamentos_respostas r ON o.id = r.orcamento_id
    LEFT JOIN usuarios u ON r.usuario_id = u.id
    WHERE o.status = 'respondido'
    GROUP BY o.id
    ORDER BY o.data_resposta DESC
");

// CSS adicional
$css_adicional = ['admin.css'];

// Incluir cabeçalho administrativo
require_once '../includes/admin_header.php';
?>

    <!-- Conteúdo Principal -->
    <div class="admin-content">
        <div class="admin-header">
            <h2>Orçamentos Respondidos</h2>
            <p>Visualize os orçamentos que já foram respondidos</p>
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
                    <h3>Nenhum orçamento respondido</h3>
                    <p>Não há orçamentos respondidos no momento.</p>
                </div>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Serviços</th>
                            <th>Valor</th>
                            <th>Respondido em</th>
                            <th>Respondido por</th>
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
                                <td><?php echo formatarMoeda($orcamento['valor_orcamento']); ?></td>
                                <td><?php echo formatarData($orcamento['data_resposta']); ?></td>
                                <td><?php echo sanitizar($orcamento['respondido_por']); ?></td>
                                <td class="actions">
                                    <a href="orcamento-visualizar.php?id=<?php echo $orcamento['id']; ?>" class="btn-icon" title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="orcamento-aprovar.php?id=<?php echo $orcamento['id']; ?>" class="btn-icon btn-approve" title="Aprovar">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <a href="orcamento-reprovar.php?id=<?php echo $orcamento['id']; ?>" class="btn-icon btn-reject" title="Reprovar">
                                        <i class="fas fa-times"></i>
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

<style>
/* Estilos para os novos botões */
.btn-approve {
    color: #28a745;
}

.btn-approve:hover {
    background-color: rgba(40, 167, 69, 0.1);
}

.btn-reject {
    color: #dc3545;
}

.btn-reject:hover {
    background-color: rgba(220, 53, 69, 0.1);
}
</style>

<?php
// Incluir rodapé administrativo
require_once '../includes/admin_footer.php';
?>
