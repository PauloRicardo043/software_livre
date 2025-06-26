<?php
// Página de gerenciamento de clientes
$titulo_pagina = 'Gerenciar Clientes';
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

// Verificar se o usuário está logado
iniciarSessao();
verificarLogin();

// Obter lista de clientes
$db = Database::getInstance();
$clientes = $db->fetchAll("SELECT * FROM clientes ORDER BY id ASC");

// CSS adicional
$css_adicional = ['admin.css'];

// Incluir cabeçalho administrativo
require_once '../includes/admin_header.php';
?>

    <!-- Conteúdo da Página -->
    <div class="admin-content">
        <div class="admin-header">
            <h2>Gerenciar Clientes</h2>
            <p>Visualize, edite e gerencie os clientes cadastrados</p>
        </div>
        
        <div class="admin-actions">
            <a href="cliente-cadastrar.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Novo Cliente
            </a>
            
            <div class="admin-search">
                <input type="text" id="searchInput" placeholder="Buscar cliente...">
                <button type="button"><i class="fas fa-search"></i></button>
            </div>
        </div>
        
        <?php if (empty($clientes)): ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <h3>Nenhum cliente cadastrado</h3>
                <p>Não há clientes cadastrados no sistema.</p>
                <a href="cliente-cadastrar.php" class="btn btn-primary">Cadastrar Cliente</a>
            </div>
        <?php else: ?>
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Data Cadastro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td>#<?php echo $cliente['id']; ?></td>
                                <td><?php echo sanitizar($cliente['nome']); ?></td>
                                <td><?php echo sanitizar($cliente['email']); ?></td>
                                <td><?php echo sanitizar($cliente['telefone']); ?></td>
                                <td><?php echo formatarData($cliente['data_cadastro']); ?></td>
                                <td class="actions">
                                    <a href="cliente-visualizar.php?id=<?php echo $cliente['id']; ?>" class="btn-icon" title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="cliente-editar.php?id=<?php echo $cliente['id']; ?>" class="btn-icon" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="cliente-excluir.php?id=<?php echo $cliente['id']; ?>" class="btn-icon btn-delete" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este cliente?')">
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

<script>
// Script para busca de clientes na tabela
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
