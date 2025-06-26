<?php
// Página de orçamentos aprovados
$titulo_pagina = 'Orçamentos Aprovados';
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

// Verificar se o usuário está logado
iniciarSessao();
verificarLogin();

// Obter orçamentos aprovados do banco de dados
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
    WHERE o.status = 'aprovado'
    GROUP BY o.id
    ORDER BY o.data_aprovacao DESC
");

// CSS adicional
$css_adicional = ['admin.css'];

// Incluir cabeçalho administrativo
require_once '../includes/admin_header.php';
?>

    <!-- Conteúdo Principal -->
    <div class="admin-content">
        <div class="admin-header">
            <h2>Orçamentos Aprovados</h2>
            <p>Visualize os orçamentos que foram aprovados</p>
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
                    <i class="fas fa-check-circle"></i>
                    <h3>Nenhum orçamento aprovado</h3>
                    <p>Não há orçamentos aprovados no momento.</p>
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
                            <th>Aprovado em</th>
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
                                <td><?php echo formatarData($orcamento['data_aprovacao']); ?></td>
                                <td><?php echo sanitizar($orcamento['respondido_por']); ?></td>
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
/* Estilo específico para a página de orçamentos aprovados */
.empty-state i {
    color: #28a745;
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
