<?php
// Página para visualizar cliente
$titulo_pagina = 'Visualizar Cliente';
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

// Verificar se o usuário está logado
iniciarSessao();
verificarLogin();

// Verificar se o ID do cliente foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setMensagem('error', 'ID do cliente não fornecido.');
    header('Location: clientes.php');
    exit;
}

$id = (int)$_GET['id'];
$db = Database::getInstance();

// Verificar se o cliente existe
$cliente = $db->fetchOne("SELECT * FROM clientes WHERE id = ?", [$id]);
if (!$cliente) {
    setMensagem('error', 'Cliente não encontrado.');
    header('Location: clientes.php');
    exit;
}

// Obter histórico de orçamentos do cliente
$orcamentos = $db->fetchAll("SELECT * FROM orcamentos WHERE email = ? ORDER BY id DESC", [$cliente['email']]);

// Obter histórico de contatos do cliente
$contatos = $db->fetchAll("SELECT * FROM contatos WHERE email = ? ORDER BY id DESC", [$cliente['email']]);

// CSS adicional
$css_adicional = ['admin.css'];

// Incluir cabeçalho administrativo
require_once '../includes/admin_header.php';
?>

    <!-- Conteúdo da Página -->
    <div class="admin-content">
        <div class="admin-header">
            <h2>Visualizar Cliente</h2>
            <p>Detalhes completos do cliente</p>
        </div>
        
        <div class="admin-actions">
            <a href="clientes.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <a href="cliente-editar.php?id=<?php echo $id; ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Editar Cliente
            </a>
        </div>
        
        <div class="admin-card">
            <div class="admin-card-header">
                <h3>Informações do Cliente</h3>
            </div>
            <div class="admin-card-body">
                <div class="cliente-info">
                    <div class="info-section">
                        <h4>Dados Pessoais</h4>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Nome:</span>
                                <span class="info-value"><?php echo sanitizar($cliente['nome']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Email:</span>
                                <span class="info-value"><?php echo sanitizar($cliente['email']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Telefone:</span>
                                <span class="info-value"><?php echo sanitizar($cliente['telefone']); ?></span>
                            </div>
                            
                            <div class="info-item">
                                <span class="info-label">Data de Cadastro:</span>
                                <span class="info-value"><?php echo formatarData($cliente['data_cadastro']); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-section">
                        <h4>Endereço</h4>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">CEP:</span>
                                <span class="info-value"><?php echo sanitizar($cliente['cep']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Cidade:</span>
                                <span class="info-value"><?php echo sanitizar($cliente['cidade']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Estado:</span>
                                <span class="info-value"><?php echo sanitizar($cliente['estado']); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($cliente['observacoes'])): ?>
                    <div class="info-section">
                        <h4>Observações</h4>
                        <div class="info-text">
                            <?php echo nl2br(sanitizar($cliente['observacoes'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Histórico de Orçamentos -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h3>Histórico de Orçamentos</h3>
            </div>
            <div class="admin-card-body">
                <?php if (empty($orcamentos)): ?>
                    <div class="empty-state small">
                        <i class="fas fa-clipboard-list"></i>
                        <h4>Nenhum orçamento encontrado</h4>
                        <p>Este cliente ainda não solicitou orçamentos.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Serviços</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orcamentos as $orcamento): ?>
                                    <tr>
                                        <td>#<?php echo $orcamento['id']; ?></td>
                                        <td>
                                            <?php 
                                            $servicos = json_decode($orcamento['servicos'], true);
                                            if (is_array($servicos)) {
                                                echo implode(', ', $servicos);
                                            } else {
                                                echo sanitizar($orcamento['servicos']);
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo formatarData($orcamento['data_cadastro']); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $orcamento['status']; ?>">
                                                <?php echo formatarStatusOrcamento($orcamento['status']); ?>
                                            </span>
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
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Histórico de Contatos -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h3>Histórico de Contatos</h3>
            </div>
            <div class="admin-card-body">
                <?php if (empty($contatos)): ?>
                    <div class="empty-state small">
                        <i class="fas fa-envelope-open"></i>
                        <h4>Nenhum contato encontrado</h4>
                        <p>Este cliente ainda não enviou mensagens de contato.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Setor</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contatos as $contato): ?>
                                    <tr>
                                        <td>#<?php echo $contato['id']; ?></td>
                                        <td><?php echo sanitizar($contato['setor']); ?></td>
                                        <td><?php echo formatarData($contato['data_cadastro']); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $contato['status']; ?>">
                                                <?php echo $contato['status'] == 'novo' ? 'Não Respondido' : 'Respondido'; ?>
                                            </span>
                                        </td>
                                        <td class="actions">
                                            <a href="contato-visualizar.php?id=<?php echo $contato['id']; ?>" class="btn-icon" title="Visualizar">
                                                <i class="fas fa-eye"></i>
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

<style>
    .cliente-info {
        margin-bottom: 20px;
    }
    
    .info-section {
        margin-bottom: 30px;
    }
    
    .info-section h4 {
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 1px solid #e0e0e0;
        color: #333;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
    }
    
    .info-item {
        display: flex;
        flex-direction: column;
    }
    
    .info-label {
        font-weight: 500;
        color: #666;
        margin-bottom: 5px;
        font-size: 0.9rem;
    }
    
    .info-value {
        font-size: 1rem;
    }
    
    .info-text {
        background-color: #f9f9f9;
        padding: 15px;
        border-radius: 4px;
        line-height: 1.6;
    }
    
    .empty-state.small {
        padding: 20px;
    }
    
    .empty-state.small i {
        font-size: 2rem;
        margin-bottom: 10px;
    }
    
    .empty-state.small h4 {
        font-size: 1.2rem;
        margin-bottom: 5px;
    }
    
    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    .status-novo {
        background-color: #FFF9C4;
        color: #F57F17;
    }
    
    .status-respondido {
        background-color: #E8F5E9;
        color: #2E7D32;
    }
    
    .status-pendente {
        background-color: #FFEBEE;
        color: #C62828;
    }
    
    .status-aprovado {
        background-color: #E8F5E9;
        color: #2E7D32;
    }
    
    .status-recusado {
        background-color: #FFEBEE;
        color: #C62828;
    }
    
    .status-expirado {
        background-color: #ECEFF1;
        color: #546E7A;
    }
    
    .table-responsive {
        overflow-x: auto;
    }
</style>

<?php
// Incluir rodapé administrativo
require_once '../includes/admin_footer.php';
?>
