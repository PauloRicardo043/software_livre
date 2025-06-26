<?php
// Página de contatos
$titulo_pagina = 'Gerenciar Contatos';
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

// Verificar se o usuário está logado
iniciarSessao();
verificarLogin();

// Determinar o status dos contatos a serem exibidos
$status = isset($_GET['status']) ? $_GET['status'] : 'novo';
if (!in_array($status, ['novo', 'respondido'])) {
    $status = 'novo';
}

// Obter contatos pelo status
$db = Database::getInstance();
$contatos = $db->fetchAll("SELECT * FROM contatos WHERE status = ? ORDER BY data_envio DESC", [$status]);

// CSS adicional
$css_adicional = ['admin.css'];

// Incluir cabeçalho administrativo
require_once '../includes/admin_header.php';
?>

    <!-- Conteúdo da Página -->
    <div class="admin-content">
        <div class="admin-header">
            <h2>Gerenciar Contatos</h2>
            <p><?php echo $status == 'novo' ? 'Contatos recebidos aguardando resposta' : 'Contatos já respondidos'; ?></p>
        </div>
        
        <!-- Tabs de navegação -->
        <div class="admin-tabs">
            <ul class="tabs-nav">
                <li class="<?php echo $status == 'novo' ? 'active' : ''; ?>">
                    <a href="contatos.php?status=novo">Contatos Recebidos</a>
                </li>
                <li class="<?php echo $status == 'respondido' ? 'active' : ''; ?>">
                    <a href="contatos.php?status=respondido">Contatos Respondidos</a>
                </li>
            </ul>
            
            <div class="tabs-content">
                <div class="tab-pane active">
                    <?php if (empty($contatos)): ?>
                        <div class="empty-state">
                            <i class="fas fa-envelope-open"></i>
                            <h3>Nenhum contato <?php echo $status == 'novo' ? 'recebido' : 'respondido'; ?></h3>
                            <p>Não há contatos <?php echo $status == 'novo' ? 'aguardando resposta' : 'respondidos'; ?> no momento.</p>
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
                                        <th>Setor</th>
                                        <th>Data</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($contatos as $contato): ?>
                                        <tr>
                                            <td>#<?php echo $contato['id']; ?></td>
                                            <td><?php echo sanitizar($contato['nome']); ?></td>
                                            <td><?php echo sanitizar($contato['email']); ?></td>
                                            <td><?php echo sanitizar($contato['telefone']); ?></td>
                                            <td><?php echo sanitizar($contato['setor']); ?></td>
                                            <td><?php echo formatarData($contato['data_envio']); ?></td>
                                            <td class="actions">
                                                <a href="contato-visualizar.php?id=<?php echo $contato['id']; ?>" class="btn-icon" title="Visualizar">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($status == 'novo'): ?>
                                                <a href="contato-responder.php?id=<?php echo $contato['id']; ?>" class="btn-icon" title="Responder">
                                                    <i class="fas fa-reply"></i>
                                                </a>
                                                <?php endif; ?>
                                                <a href="contato-excluir.php?id=<?php echo $contato['id']; ?>" class="btn-icon btn-delete" title="Excluir">
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
            </div>
        </div>
    </div>

<?php
// Incluir rodapé administrativo
require_once '../includes/admin_footer.php';
?>
