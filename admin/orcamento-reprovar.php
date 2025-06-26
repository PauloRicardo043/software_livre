<?php
// Página para reprovar orçamento
$titulo_pagina = 'Reprovar Orçamento';
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

// Processar a reprovação se confirmada
if (isset($_POST['confirmar']) && $_POST['confirmar'] === 'sim') {
    // Atualizar o status do orçamento para recusado
    $resultado = $db->update('orcamentos', [
        'status' => 'recusado',
        'data_recusa' => date('Y-m-d H:i:s'),
        'motivo_recusa' => isset($_POST['motivo_recusa']) ? $_POST['motivo_recusa'] : 'Não especificado'
    ], 'id = ?', [$id]);
    
    if ($resultado) {
        // Registrar log
        registrarLog('orcamento_recusado', 'Orçamento #' . $id . ' recusado com sucesso');
        
        // Enviar notificação ao cliente (opcional)
        // enviarEmailRecusaOrcamento($orcamento['email'], $orcamento['nome'], $id, $_POST['motivo_recusa']);
        
        setMensagem('success', 'Orçamento recusado com sucesso.');
        header('Location: orcamentos-recusados.php');
        exit;
    } else {
        setMensagem('error', 'Erro ao recusar o orçamento. Tente novamente.');
        header('Location: orcamentos-respondidos.php');
        exit;
    }
} elseif (isset($_POST['confirmar']) && $_POST['confirmar'] === 'nao') {
    // Usuário cancelou a reprovação
    setMensagem('info', 'Reprovação cancelada pelo usuário.');
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
        <h2>Reprovar Orçamento</h2>
        <p>Confirme a reprovação do orçamento #<?php echo $id; ?></p>
    </div>
    
    <div class="admin-card">
        <div class="admin-card-header">
            <h3>Confirmação de Reprovação</h3>
        </div>
        <div class="admin-card-body">
            <div class="confirmation-dialog">
                <div class="confirmation-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="confirmation-message">
                    <h4>Tem certeza que deseja reprovar este orçamento?</h4>
                    <p>Ao reprovar, o orçamento será movido para a lista de orçamentos recusados e o cliente poderá ser notificado.</p>
                    
                    <div class="orcamento-info">
                        <p><strong>Cliente:</strong> <?php echo sanitizar($orcamento_detalhes['nome']); ?></p>
                        <p><strong>Email:</strong> <?php echo sanitizar($orcamento_detalhes['email']); ?></p>
                        <p><strong>Telefone:</strong> <?php echo sanitizar($orcamento_detalhes['telefone']); ?></p>
                        <p><strong>Serviços:</strong> <?php echo sanitizar($orcamento_detalhes['servicos_nomes']); ?></p>
                        <p><strong>Valor:</strong> <?php echo formatarMoeda($orcamento_detalhes['valor_orcamento']); ?></p>
                        <p><strong>Respondido por:</strong> <?php echo sanitizar($orcamento_detalhes['respondido_por']); ?></p>
                        <p><strong>Data da resposta:</strong> <?php echo formatarData($orcamento_detalhes['data_resposta']); ?></p>
                    </div>
                    
                    <div class="form-group mt-4">
                        <label for="motivo_recusa"><strong>Motivo da Recusa:</strong></label>
                        <textarea id="motivo_recusa" name="motivo_recusa" class="form-control" rows="3" placeholder="Informe o motivo da recusa do orçamento"></textarea>
                    </div>
                </div>
            </div>
            
            <div class="confirmation-actions">
                <form method="post" action="orcamento-reprovar.php?id=<?php echo $id; ?>">
                    <textarea name="motivo_recusa" id="motivo_recusa_hidden" style="display: none;"></textarea>
                    <button type="submit" name="confirmar" value="nao" class="btn btn-secondary">Não, Cancelar</button>
                    <button type="submit" name="confirmar" value="sim" class="btn btn-danger">Sim, Reprovar</button>
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
        color: #dc3545;
        margin-right: 20px;
    }
    
    .confirmation-message {
        flex: 1;
    }
    
    .confirmation-message h4 {
        color: #dc3545;
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
        background-color: #dc3545;
    }
    
    .btn-danger:hover {
        background-color: #c82333;
    }
    
    .mt-4 {
        margin-top: 1.5rem;
    }
    
    .form-control {
        display: block;
        width: 100%;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
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

<script>
    // Sincronizar os campos de motivo de recusa
    document.addEventListener('DOMContentLoaded', function() {
        const motivoRecusa = document.getElementById('motivo_recusa');
        const motivoRecusaHidden = document.getElementById('motivo_recusa_hidden');
        
        if (motivoRecusa && motivoRecusaHidden) {
            motivoRecusa.addEventListener('input', function() {
                motivoRecusaHidden.value = this.value;
            });
        }
    });
</script>

<?php
// Incluir rodapé administrativo
require_once '../includes/admin_footer.php';
?>
