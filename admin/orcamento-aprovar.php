<?php
// Página para aprovar orçamento
$titulo_pagina = 'Aprovar Orçamento';
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

// Verificar se o usuário está logado
iniciarSessao();
verificarLogin();

// Verificar se o ID do orçamento foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setMensagem('error', 'ID do orçamento não fornecido.');
    header('Location: orcamentos-respondidos.php');
    exit;
}

$id = (int)$_GET['id'];
$db = Database::getInstance();

// Verificar se o orçamento existe e está no status respondido
$orcamento = $db->fetchOne("SELECT * FROM orcamentos WHERE id = ? AND status = 'respondido'", [$id]);
if (!$orcamento) {
    setMensagem('error', 'Orçamento não encontrado ou não está no status respondido.');
    header('Location: orcamentos-respondidos.php');
    exit;
}

// Processar a aprovação se confirmada
if (isset($_POST['confirmar']) && $_POST['confirmar'] === 'sim') {
    // Atualizar o status do orçamento para aprovado
    $resultado = $db->update('orcamentos', [
        'status' => 'aprovado',
        'data_aprovacao' => date('Y-m-d H:i:s')
    ], 'id = ?', [$id]);
    
    if ($resultado) {
        // Registrar log
        registrarLog('orcamento_aprovado', 'Orçamento #' . $id . ' aprovado com sucesso');
        
        // Enviar notificação ao cliente (opcional)
        // enviarEmailAprovacaoOrcamento($orcamento['email'], $orcamento['nome'], $id);
        
        setMensagem('success', 'Orçamento aprovado com sucesso.');
        header('Location: orcamentos-aprovados.php');
        exit;
    } else {
        setMensagem('error', 'Erro ao aprovar o orçamento. Tente novamente.');
        header('Location: orcamentos-respondidos.php');
        exit;
    }
} elseif (isset($_POST['confirmar']) && $_POST['confirmar'] === 'nao') {
    // Usuário cancelou a aprovação
    setMensagem('info', 'Aprovação cancelada pelo usuário.');
    header('Location: orcamentos-respondidos.php');
    exit;
}

// Obter detalhes do orçamento para exibição
$orcamento_detalhes = $db->fetchOne("
    SELECT o.*, 
           GROUP_CONCAT(s.nome SEPARATOR ', ') as servicos_nomes,
           u.nome as respondido_por,
           r.valor as valor_orcamento
    FROM orcamentos o
    LEFT JOIN orcamentos_servicos os ON o.id = os.orcamento_id
    LEFT JOIN servicos s ON os.servico_id = s.id
    LEFT JOIN orcamentos_respostas r ON o.id = r.orcamento_id
    LEFT JOIN usuarios u ON r.usuario_id = u.id
    WHERE o.id = ?
    GROUP BY o.id
", [$id]);

// CSS adicional
$css_adicional = ['admin.css'];

// Incluir cabeçalho administrativo
require_once '../includes/admin_header.php';
?>

<div class="admin-content">
    <div class="admin-header">
        <h2>Aprovar Orçamento</h2>
        <p>Confirme a aprovação do orçamento #<?php echo $id; ?></p>
    </div>
    
    <div class="admin-card">
        <div class="admin-card-header">
            <h3>Confirmação de Aprovação</h3>
        </div>
        <div class="admin-card-body">
            <div class="confirmation-dialog">
                <div class="confirmation-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="confirmation-message">
                    <h4>Tem certeza que deseja aprovar este orçamento?</h4>
                    <p>Ao aprovar, o orçamento será movido para a lista de orçamentos aprovados e o cliente poderá ser notificado.</p>
                    
                    <div class="orcamento-info">
                        <p><strong>Cliente:</strong> <?php echo sanitizar($orcamento_detalhes['nome']); ?></p>
                        <p><strong>Email:</strong> <?php echo sanitizar($orcamento_detalhes['email']); ?></p>
                        <p><strong>Telefone:</strong> <?php echo sanitizar($orcamento_detalhes['telefone']); ?></p>
                        <p><strong>Serviços:</strong> <?php echo sanitizar($orcamento_detalhes['servicos_nomes']); ?></p>
                        <p><strong>Valor:</strong> <?php echo formatarMoeda($orcamento_detalhes['valor_orcamento']); ?></p>
                        <p><strong>Respondido por:</strong> <?php echo sanitizar($orcamento_detalhes['respondido_por']); ?></p>
                        <p><strong>Data da resposta:</strong> <?php echo formatarData($orcamento_detalhes['data_resposta']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="confirmation-actions">
                <form method="post" action="orcamento-aprovar.php?id=<?php echo $id; ?>">
                    <button type="submit" name="confirmar" value="nao" class="btn btn-secondary">Não, Cancelar</button>
                    <button type="submit" name="confirmar" value="sim" class="btn btn-success">Sim, Aprovar</button>
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
        color: #28a745;
        margin-right: 20px;
    }
    
    .confirmation-message {
        flex: 1;
    }
    
    .confirmation-message h4 {
        color: #28a745;
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
    
    .btn-success {
        background-color: #28a745;
    }
    
    .btn-success:hover {
        background-color: #218838;
    }
    
    @media (max-width: 768px) {
        .confirmation-dialog {
            flex-direction: column;
        }
        
        .confirmation-icon {
            margin-right: 0;
            margin-bottom: 15px;
            text-align: center;
        }
    }
</style>

<?php
// Incluir rodapé administrativo
require_once '../includes/admin_footer.php';
?>
