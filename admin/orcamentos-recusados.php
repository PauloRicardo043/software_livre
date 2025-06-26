<?php
// Página de orçamentos recusados
$titulo_pagina = 'Orçamentos Recusados';
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

// Verificar se o usuário está logado
iniciarSessao();
verificarLogin();

// Obter orçamentos recusados do banco de dados
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
    WHERE o.status = 'recusado'
    GROUP BY o.id
    ORDER BY o.data_recusa DESC
");

// CSS adicional
$css_adicional = ['admin.css'];

// Incluir cabeçalho administrativo
require_once '../includes/admin_header.php';
?>

    <!-- Conteúdo Principal -->
    <div class="admin-content">
        <div class="admin-header">
            <h2>Orçamentos Recusados</h2>
            <p>Visualize os orçamentos que foram recusados</p>
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
                    <i class="fas fa-times-circle"></i>
                    <h3>Nenhum orçamento recusado</h3>
                    <p>Não há orçamentos recusados no momento.</p>
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
                            <th>Recusado em</th>
                            <th>Motivo</th>
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
                                <td><?php echo formatarData($orcamento['data_recusa']); ?></td>
                                <td>
                                    <?php if (!empty($orcamento['motivo_recusa'])): ?>
                                        <span class="tooltip-container" data-tooltip="<?php echo sanitizar($orcamento['motivo_recusa']); ?>">
                                            <i class="fas fa-info-circle"></i> Ver motivo
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">Não especificado</span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions">
                                    <a href="orcamento-visualizar.php?id=<?php echo $orcamento['id']; ?>" class="btn-icon" title="Visualizar">
                                        <i class="fas fa-eye"></i>
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
/* Estilo específico para a página de orçamentos recusados */
.empty-state i {
    color: #dc3545;
}

.tooltip-container {
    position: relative;
    cursor: pointer;
    color: #0056b3;
}

.tooltip-container:hover::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background-color: #333;
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 14px;
    white-space: nowrap;
    z-index: 1;
    min-width: 200px;
    white-space: normal;
}

.text-muted {
    color: #6c757d;
}
</style>

<script>
// Script para busca na tabela
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('.admin-table tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});
</script>

<?php
// Incluir rodapé administrativo
require_once '../includes/admin_footer.php';
?>
