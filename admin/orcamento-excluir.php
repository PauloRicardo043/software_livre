<?php
// Página de exclusão de orçamento
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

// Verificar se o usuário está logado
iniciarSessao();
verificarLogin();

// Verificar se o ID do orçamento foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setMensagem('error', 'ID do orçamento não fornecido.');
    header('Location: orcamentos-pendentes.php');
    exit;
}

$id = (int)$_GET['id'];
$db = Database::getInstance();

// Verificar se o orçamento existe
$orcamento = $db->fetchOne("SELECT * FROM orcamentos WHERE id = ?", [$id]);
if (!$orcamento) {
    setMensagem('error', 'Orçamento não encontrado.');
    header('Location: orcamentos-pendentes.php');
    exit;
}

// Processar a exclusão se confirmada
if (isset($_POST['confirmar']) && $_POST['confirmar'] === 'sim') {
    // Excluir o orçamento
    $resultado = $db->delete('orcamentos', 'id = ?', [$id]);
    
    if ($resultado) {
        // Registrar log
        registrarLog('orcamento_excluido', 'Orçamento #' . $id . ' excluído com sucesso');
        
        setMensagem('success', 'Orçamento excluído com sucesso.');
        
        // Redirecionar de acordo com o status do orçamento
        switch ($orcamento['status']) {
            case 'pendente':
                header('Location: orcamentos-pendentes.php');
                break;
            case 'respondido':
                header('Location: orcamentos-respondidos.php');
                break;
            case 'aprovado':
                header('Location: orcamentos-aprovados.php');
                break;
            case 'recusado':
                header('Location: orcamentos-recusados.php');
                break;
            case 'expirado':
                header('Location: orcamentos-expirados.php');
                break;
            default:
                header('Location: dashboard.php');
                break;
        }
        exit;
    } else {
        setMensagem('error', 'Erro ao excluir o orçamento. Tente novamente.');
    }
} elseif (isset($_POST['confirmar']) && $_POST['confirmar'] === 'nao') {
    // Usuário cancelou a exclusão
    setMensagem('info', 'Exclusão cancelada pelo usuário.');
    
    // Redirecionar de acordo com o status do orçamento
    switch ($orcamento['status']) {
        case 'pendente':
            header('Location: orcamentos-pendentes.php');
            break;
        case 'respondido':
            header('Location: orcamentos-respondidos.php');
            break;
        case 'aprovado':
            header('Location: orcamentos-aprovados.php');
            break;
        case 'recusado':
            header('Location: orcamentos-recusados.php');
            break;
        case 'expirado':
            header('Location: orcamentos-expirados.php');
            break;
        default:
            header('Location: dashboard.php');
            break;
    }
    exit;
}

// Definir título da página
$titulo_pagina = 'Excluir Orçamento';
$css_adicional = ['admin.css'];

// Incluir cabeçalho
require_once '../includes/admin_header.php';
?>

<div class="admin-content">
    <div class="admin-header">
        <h2>Excluir Orçamento</h2>
        <p>Confirme a exclusão do orçamento</p>
    </div>
    
    <div class="admin-card">
        <div class="admin-card-header">
            <h3>Confirmação de Exclusão</h3>
        </div>
        <div class="admin-card-body">
            <div class="confirmation-dialog">
                <div class="confirmation-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="confirmation-message">
                    <h4>Tem certeza que deseja excluir este orçamento?</h4>
                    <p>Esta ação não pode ser desfeita. Todos os dados relacionados a este orçamento serão permanentemente removidos do sistema.</p>
                    
                    <div class="orcamento-info">
                        <p><strong>ID:</strong> #<?php echo $orcamento['id']; ?></p>
                        <p><strong>Cliente:</strong> <?php echo sanitizar($orcamento['nome']); ?></p>
                        <p><strong>Email:</strong> <?php echo sanitizar($orcamento['email']); ?></p>
                        <p><strong>Data:</strong> <?php echo formatarData($orcamento['data_solicitacao']); ?></p>
                        <p><strong>Status:</strong> <?php echo formatarStatusOrcamento($orcamento['status']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="confirmation-actions">
                <form method="post" action="orcamento-excluir.php?id=<?php echo $id; ?>">
                    <button type="submit" name="confirmar" value="nao" class="btn btn-secondary">Não, Cancelar</button>
                    <button type="submit" name="confirmar" value="sim" class="btn btn-danger">Sim, Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .confirmation-dialog {
        display: flex;
        align-items: flex-start;
        margin-bottom: 30px;
    }
    
    .confirmation-icon {
        font-size: 3rem;
        color: #F44336;
        margin-right: 20px;
    }
    
    .confirmation-message {
        flex: 1;
    }
    
    .confirmation-message h4 {
        color: #F44336;
        margin-top: 0;
        margin-bottom: 15px;
    }
    
    .orcamento-info {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 4px;
        margin-top: 20px;
    }
    
    .orcamento-info p {
        margin: 5px 0;
    }
    
    .confirmation-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    
    .btn-danger {
        background-color: #F44336;
    }
    
    .btn-danger:hover {
        background-color: #d32f2f;
    }
</style>

<?php
// Incluir rodapé
require_once '../includes/admin_footer.php';
?>
