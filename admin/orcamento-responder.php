<?php
// Página de resposta/edição de orçamento
$titulo_pagina = 'Responder Orçamento';
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

// Verificar se já existe uma resposta para este orçamento
$resposta = null;
$modo_edicao = false;

if ($orcamento['status'] == 'respondido') {
    $resposta = $db->fetchOne("
        SELECT r.*, u.nome as respondido_por
        FROM orcamentos_respostas r
        JOIN usuarios u ON r.usuario_id = u.id
        WHERE r.orcamento_id = ?
        ORDER BY r.data_resposta DESC
        LIMIT 1
    ", [$id]);
    
    $modo_edicao = true;
    $titulo_pagina = 'Editar Resposta de Orçamento';
}

// Processar o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar dados
    $resposta_texto = isset($_POST['resposta']) ? trim($_POST['resposta']) : '';
    $valor = isset($_POST['valor']) ? str_replace(['R$', '.', ','], ['', '', '.'], $_POST['valor']) : '';
    
    $erros = [];
    
    // Validações básicas
    if (empty($resposta_texto)) {
        $erros[] = 'A resposta é obrigatória.';
    }
    
    if (empty($valor) || !is_numeric($valor)) {
        $erros[] = 'O valor do orçamento é obrigatório e deve ser numérico.';
    }
    
    // Se não houver erros, salvar no banco de dados
    if (empty($erros)) {
        try {
            // Iniciar transação
            $db->getConnection()->beginTransaction();
            
            if ($modo_edicao) {
                // Atualizar resposta existente
                $db->update('orcamentos_respostas', [
                    'resposta' => $resposta_texto,
                    'valor' => $valor,
                    'data_resposta' => date('Y-m-d H:i:s')
                ], 'id = ?', [$resposta['id']]);
                
                // Atualizar data de resposta no orçamento
                $db->update('orcamentos', [
                    'data_resposta' => date('Y-m-d H:i:s')
                ], 'id = ?', [$id]);
                
                $mensagem_sucesso = 'Resposta atualizada com sucesso!';
            } else {
                // Inserir nova resposta
                $db->insert('orcamentos_respostas', [
                    'orcamento_id' => $id,
                    'usuario_id' => $_SESSION['usuario_id'],
                    'resposta' => $resposta_texto,
                    'valor' => $valor,
                    'data_resposta' => date('Y-m-d H:i:s')
                ]);
                
                // Atualizar status do orçamento para respondido
                $db->update('orcamentos', [
                    'status' => 'respondido',
                    'data_resposta' => date('Y-m-d H:i:s')
                ], 'id = ?', [$id]);
                
                $mensagem_sucesso = 'Orçamento respondido com sucesso!';
                
                // Enviar e-mail para o cliente
                $email_cliente = $orcamento['email'];
                $nome_cliente = $orcamento['nome'];
                $telefone_cliente = $orcamento['telefone'];
               $email_usuario = $_SESSION['usuario_email'] ?? null;
                $nome_usuario = $_SESSION['usuario_nome'];
                
                // Preparar o assunto do e-mail
                $assunto = "Resposta ao seu orçamento - IDEA Service";
                
                // Preparar o corpo do e-mail
                $corpo_email = "Olá {$nome_cliente},\n\n";
                $corpo_email .= "Agradecemos pelo seu interesse nos serviços da IDEA Service.\n\n";
                $corpo_email .= "Segue abaixo nossa resposta ao seu orçamento para os serviços solicitados:\n\n";
                $corpo_email .= "Serviços: {$orcamento['servicos_nomes']}\n";
                $corpo_email .= "Valor: " . formatarMoeda($valor) . "\n\n";
                $corpo_email .= "Detalhes do orçamento:\n";
                $corpo_email .= $resposta_texto . "\n\n";
                $corpo_email .= "Para mais informações ou para aprovar este orçamento, entre em contato conosco pelo telefone ou responda a este e-mail.\n\n";
                $corpo_email .= "Atenciosamente,\n{$nome_usuario}\nIDEA Service";
                
                // Tentar enviar o e-mail
                $email_enviado = enviarEmail($email_cliente, $assunto, $corpo_email, $email_usuario, $nome_usuario);
                
                if ($email_enviado) {
                    $mensagem_sucesso .= ' E-mail enviado para o cliente.';
                } else {
                    $mensagem_sucesso .= ' Não foi possível enviar o e-mail para o cliente.';
                }
                
                // Preparar mensagem para WhatsApp
                $telefone_limpo = preg_replace('/[^0-9]/', '', $telefone_cliente);
                if (strlen($telefone_limpo) > 0) {
                    // Garantir que o telefone tenha o formato correto para o WhatsApp
                    if (substr($telefone_limpo, 0, 2) != '55') {
                        $telefone_limpo = '55' . $telefone_limpo;
                    }
                    
                    // Preparar a mensagem para WhatsApp
                    $mensagem_whatsapp = "Olá {$nome_cliente}, a IDEA Service agradece pelo seu interesse. Seu orçamento para {$orcamento['servicos_nomes']} está pronto! Valor: " . formatarMoeda($valor) . ". Para mais detalhes, verifique seu e-mail ou entre em contato conosco.";
                    $mensagem_whatsapp = urlencode($mensagem_whatsapp);
                    
                    // Armazenar o link do WhatsApp para ser exibido após o redirecionamento
                    $_SESSION['whatsapp_link'] = "https://api.whatsapp.com/send?phone={$telefone_limpo}&text={$mensagem_whatsapp}";
                }
            }
            
            // Confirmar transação
            $db->getConnection()->commit();
            
            // Mensagem de sucesso
            setMensagem('success', $mensagem_sucesso);
            
            // Redirecionar para a página de visualização
            header('Location: orcamento-visualizar.php?id=' . $id);
            exit;
        } catch (Exception $e) {
            // Reverter transação em caso de erro
            $db->getConnection()->rollBack();
            setMensagem('error', 'Erro ao salvar resposta. Por favor, tente novamente.');
        }
    } else {
        // Exibir erros
        setMensagem('error', implode('<br>', $erros));
    }
}

// CSS adicional
$css_adicional = ['admin.css'];

// Incluir cabeçalho administrativo
require_once '../includes/admin_header.php';
?>

    <!-- Conteúdo Principal -->
    <div class="admin-content">
        <div class="admin-header">
            <h2><?php echo $modo_edicao ? 'Editar Resposta' : 'Responder Orçamento'; ?> #<?php echo $orcamento['id']; ?></h2>
            <p><?php echo $modo_edicao ? 'Atualize a resposta ao orçamento' : 'Envie uma resposta ao orçamento solicitado'; ?></p>
        </div>
        
        <div class="admin-actions">
            <a href="javascript:history.back()" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
            <a href="orcamento-visualizar.php?id=<?php echo $orcamento['id']; ?>" class="btn btn-primary"><i class="fas fa-eye"></i> Visualizar Orçamento</a>
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
                        <span class="info-label">Serviços:</span>
                        <span class="info-value"><?php echo sanitizar($orcamento['servicos_nomes']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Data da Solicitação:</span>
                        <span class="info-value"><?php echo formatarData($orcamento['data_solicitacao']); ?></span>
                    </div>
                </div>
                
                <?php if (!empty($orcamento['mensagem'])): ?>
                <div class="message-content">
                    <h4>Mensagem do Cliente:</h4>
                    <p><?php echo nl2br(sanitizar($orcamento['mensagem'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="admin-card">
            <div class="admin-card-header">
                <h3><?php echo $modo_edicao ? 'Editar Resposta' : 'Responder Orçamento'; ?></h3>
            </div>
            <div class="admin-card-body">
                <form action="orcamento-responder.php?id=<?php echo $orcamento['id']; ?>" method="post" id="responseForm">
                    <input type="hidden" name="csrf_token" value="<?php echo gerarTokenCSRF(); ?>">
                    
                    <div class="form-group">
                        <label for="valor">Valor do Orçamento</label>
                        <input type="text" id="valor" name="valor" required placeholder="R$ 0,00" value="<?php echo isset($_POST['valor']) ? sanitizar($_POST['valor']) : ($modo_edicao && $resposta ? formatarMoeda($resposta['valor']) : ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="resposta">Resposta ao Cliente</label>
                        <textarea id="resposta" name="resposta" required rows="10" placeholder="Digite aqui sua resposta detalhada ao orçamento..."><?php echo isset($_POST['resposta']) ? sanitizar($_POST['resposta']) : ($modo_edicao && $resposta ? sanitizar($resposta['resposta']) : ''); ?></textarea>
                    </div>
                    
                    <div class="form-info">
                        <p><i class="fas fa-info-circle"></i> Ao enviar a resposta, um e-mail será enviado automaticamente para o cliente usando seu endereço de e-mail (<?php echo $_SESSION['usuario_email']; ?>). Além disso, um link para envio de mensagem via WhatsApp será disponibilizado.</p>
                    </div>
                    
                    <button type="submit" class="btn btn-primary"><?php echo $modo_edicao ? 'Atualizar Resposta' : 'Enviar Resposta'; ?></button>
                </form>
            </div>
        </div>
    </div>

<style>
    .form-info {
        background-color: #e3f2fd;
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 20px;
        border-left: 4px solid #2196F3;
    }
    
    .form-info p {
        margin: 0;
        color: #0d47a1;
    }
    
    .form-info i {
        margin-right: 5px;
    }
    
    .message-content {
        background-color: #f9f9f9;
        padding: 15px;
        border-radius: 4px;
        margin-top: 20px;
        border-left: 4px solid #4CAF50;
    }
    
    .message-content h4 {
        margin-top: 0;
        color: #2E7D32;
    }
</style>

<?php
// Incluir rodapé administrativo
require_once '../includes/admin_footer.php';
?>
