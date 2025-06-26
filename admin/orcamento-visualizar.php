<?php
// Página de visualização de orçamento
$titulo_pagina = 'Visualizar Orçamento';
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

// Verificar se o usuário está logado
iniciarSessao();
verificarLogin();

// Verificar se o ID foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setMensagem('error', 'ID do orçamento não fornecido.');
    header('Location: orcamentos-pendentes.php');
    exit;
}

$id = (int)$_GET['id'];

// Obter dados do orçamento
$db = Database::getInstance();
$orcamento = $db->fetchOne("
    SELECT o.*, 
           GROUP_CONCAT(s.nome SEPARATOR ', ') as servicos_nomes,
           GROUP_CONCAT(s.id SEPARATOR ',') as servicos_ids
    FROM orcamentos o
    LEFT JOIN orcamentos_servicos os ON o.id = os.orcamento_id
    LEFT JOIN servicos s ON os.servico_id = s.id
    WHERE o.id = ?
    GROUP BY o.id
", [$id]);

// Verificar se o orçamento existe
if (!$orcamento) {
    setMensagem('error', 'Orçamento não encontrado.');
    header('Location: orcamentos-pendentes.php');
    exit;
}

// Obter resposta do orçamento, se houver
$resposta = null;
if ($orcamento['status'] == 'respondido') {
    $resposta = $db->fetchOne("
        SELECT r.*, u.nome as respondido_por
        FROM orcamentos_respostas r
        JOIN usuarios u ON r.usuario_id = u.id
        WHERE r.orcamento_id = ?
        ORDER BY r.data_resposta DESC
        LIMIT 1
    ", [$id]);
}

// Obter lista de serviços para exibição
$servicos_ids = explode(',', $orcamento['servicos_ids']);
$servicos = $db->fetchAll("SELECT * FROM servicos WHERE ativo = 1 ORDER BY nome");

// CSS adicional
$css_adicional = ['admin.css'];

// Incluir cabeçalho administrativo
require_once '../includes/admin_header.php';
?>

    <!-- Conteúdo Principal -->
    <div class="admin-content">
        <div class="admin-header">
            <h2>Visualizar Orçamento #<?php echo $orcamento['id']; ?></h2>
            <p>Detalhes completos do orçamento</p>
        </div>
        
        <div class="admin-actions">
            <a href="javascript:history.back()" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
            
            <?php if ($orcamento['status'] == 'pendente' || $orcamento['status'] == 'expirado'): ?>
                <a href="orcamento-responder.php?id=<?php echo $orcamento['id']; ?>" class="btn btn-primary"><i class="fas fa-reply"></i> Responder</a>
            <?php elseif ($orcamento['status'] == 'respondido'): ?>
                <a href="orcamento-editar.php?id=<?php echo $orcamento['id']; ?>" class="btn btn-primary"><i class="fas fa-edit"></i> Editar Resposta</a>
            <?php endif; ?>
            
            <a href="orcamento-excluir.php?id=<?php echo $orcamento['id']; ?>" class="btn btn-danger btn-delete"><i class="fas fa-trash"></i> Excluir</a>
        </div>
        
        <div class="admin-card">
            <div class="admin-card-header">
                <h3>Informações do Cliente</h3>
            </div>
            <div class="admin-card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Nome:</span>
                        <span class="info-value"><?php echo sanitizar($orcamento['nome']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?php echo sanitizar($orcamento['email']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Telefone:</span>
                        <span class="info-value"><?php echo sanitizar($orcamento['telefone']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Data da Solicitação:</span>
                        <span class="info-value"><?php echo formatarData($orcamento['data_solicitacao']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status:</span>
                        <span class="info-value">
                            <span class="status-badge status-<?php echo $orcamento['status']; ?>">
                                <?php echo formatarStatusOrcamento($orcamento['status']); ?>
                            </span>
                        </span>
                    </div>
                    <?php if ($orcamento['status'] == 'respondido' && $orcamento['data_resposta']): ?>
                        <div class="info-item">
                            <span class="info-label">Data da Resposta:</span>
                            <span class="info-value"><?php echo formatarData($orcamento['data_resposta']); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($orcamento['status'] == 'expirado' && $orcamento['data_expiracao']): ?>
                        <div class="info-item">
                            <span class="info-label">Data de Expiração:</span>
                            <span class="info-value"><?php echo formatarData($orcamento['data_expiracao']); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="admin-card">
            <div class="admin-card-header">
                <h3>Serviços Solicitados</h3>
            </div>
            <div class="admin-card-body">
                <div class="services-list">
                    <?php foreach ($servicos as $servico): ?>
                        <?php $checked = in_array($servico['id'], $servicos_ids); ?>
                        <div class="service-item <?php echo $checked ? 'selected' : ''; ?>">
                            <div class="service-check">
                                <i class="fas <?php echo $checked ? 'fa-check-circle' : 'fa-circle'; ?>"></i>
                            </div>
                            <div class="service-info">
                                <h4><?php echo sanitizar($servico['nome']); ?></h4>
                                <p><?php echo sanitizar($servico['descricao']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="admin-card">
            <div class="admin-card-header">
                <h3>Mensagem do Cliente</h3>
            </div>
            <div class="admin-card-body">
                <div class="message-content">
                    <?php if (!empty($orcamento['mensagem'])): ?>
                        <p><?php echo nl2br(sanitizar($orcamento['mensagem'])); ?></p>
                    <?php else: ?>
                        <p class="text-muted">Nenhuma mensagem adicional fornecida pelo cliente.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php if ($resposta): ?>
        <div class="admin-card">
            <div class="admin-card-header">
                <h3>Resposta ao Orçamento</h3>
            </div>
            <div class="admin-card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Respondido por:</span>
                        <span class="info-value"><?php echo sanitizar($resposta['respondido_por']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Data da Resposta:</span>
                        <span class="info-value"><?php echo formatarData($resposta['data_resposta']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Valor do Orçamento:</span>
                        <span class="info-value"><?php echo formatarMoeda($resposta['valor']); ?></span>
                    </div>
                </div>
                <div class="message-content">
                    <h4>Detalhes da Resposta:</h4>
                    <p><?php echo nl2br(sanitizar($resposta['resposta'])); ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

<?php
// Incluir rodapé administrativo
require_once '../includes/admin_footer.php';
?>
