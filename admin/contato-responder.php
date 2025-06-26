<?php
// Página para responder contatos
$titulo_pagina = 'Responder Contato';
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

// Verificar se o usuário está logado
iniciarSessao();
verificarLogin();

// Verificar se o ID do contato foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setMensagem('error', 'ID do contato não fornecido.');
    header('Location: contatos.php');
    exit;
}

$id = (int)$_GET['id'];
$db = Database::getInstance();

// Verificar se o contato existe
$contato = $db->fetchOne("SELECT * FROM contatos WHERE id = ?", [$id]);
if (!$contato) {
    setMensagem('error', 'Contato não encontrado.');
    header('Location: contatos.php');
    exit;
}

// Processar o formulário de resposta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resposta'])) {
    $resposta = trim($_POST['resposta']);
    $assunto = trim($_POST['assunto']);
    
    if (empty($resposta)) {
        setMensagem('error', 'Por favor, digite uma resposta.');
    } else {
        // Atualizar o status do contato para respondido
        $resultado = $db->update('contatos', [
            'status' => 'respondido',
            'resposta' => $resposta,
            'data_resposta' => date('Y-m-d H:i:s'),
            'cliente_id' => $_SESSION['cliente_id']
        ], 'id = ?', [$id]);
        
        if ($resultado) {
            // Enviar e-mail de resposta
            $email_usuario = $_SESSION['usuario_email'];
            $nome_usuario = $_SESSION['usuario_nome'];
            $email_cliente = $contato['email'];
            $nome_cliente = $contato['nome'];
            
            // Preparar o corpo do e-mail
            $corpo_email = "Olá {$nome_cliente},\n\n";
            $corpo_email .= "Agradecemos pelo seu contato com a IDEA Service.\n\n";
            $corpo_email .= "Sua mensagem: \"{$contato['mensagem']}\"\n\n";
            $corpo_email .= "Nossa resposta: \"{$resposta}\"\n\n";
            $corpo_email .= "Atenciosamente,\n{$nome_usuario}\nIDEA Service";
            
            // Tentar enviar o e-mail
            $email_enviado = enviarEmail($email_cliente, $assunto, $corpo_email, $email_usuario, $nome_usuario);
            
            if ($email_enviado) {
                setMensagem('success', 'Contato respondido com sucesso e e-mail enviado para o cliente.');
            } else {
                setMensagem('warning', 'Contato respondido com sucesso, mas houve um problema ao enviar o e-mail.');
            }
            
            // Registrar log
            registrarLog('contato_respondido', 'Contato #' . $id . ' respondido');
            
            header('Location: contatos.php?status=respondido');
            exit;
        } else {
            setMensagem('error', 'Erro ao responder o contato. Tente novamente.');
        }
    }
}

// CSS adicional
$css_adicional = ['admin.css'];

// Incluir cabeçalho administrativo
require_once '../includes/admin_header.php';
?>

    <!-- Conteúdo da Página -->
    <div class="admin-content">
        <div class="admin-header">
            <h2>Responder Contato</h2>
            <p>Responda à solicitação de contato do cliente</p>
        </div>
        
        <div class="admin-card">
            <div class="admin-card-header">
                <h3>Detalhes do Contato</h3>
            </div>
            <div class="admin-card-body">
                <div class="contact-details">
                    <div class="contact-info">
                        <p><strong>Nome:</strong> <?php echo sanitizar($contato['nome']); ?></p>
                        <p><strong>Email:</strong> <?php echo sanitizar($contato['email']); ?></p>
                        <p><strong>Telefone:</strong> <?php echo sanitizar($contato['telefone']); ?></p>
                        <p><strong>Setor:</strong> <?php echo sanitizar($contato['setor']); ?></p>
                        <p><strong>Data:</strong> <?php echo formatarData($contato['data_envio']); ?></p>
                    </div>
                    
                    <div class="contact-message">
                        <h4>Mensagem do Cliente:</h4>
                        <div class="message-content">
                            <?php echo nl2br(sanitizar($contato['mensagem'])); ?>
                        </div>
                    </div>
                </div>
                
                <form method="post" action="contato-responder.php?id=<?php echo $id; ?>" class="admin-form">
                    <div class="form-group">
                        <label for="assunto">Assunto da Resposta:</label>
                        <input type="text" id="assunto" name="assunto" value="RE: Contato IDEA Service - <?php echo sanitizar($contato['setor']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="resposta">Sua Resposta:</label>
                        <textarea id="resposta" name="resposta" rows="10" required></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <a href="contatos.php" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Enviar Resposta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<style>
    .contact-details {
        margin-bottom: 30px;
        border-bottom: 1px solid #e0e0e0;
        padding-bottom: 20px;
    }
    
    .contact-info {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .contact-info p {
        margin: 5px 0;
    }
    
    .contact-message h4 {
        margin-bottom: 10px;
        color: #333;
    }
    
    .message-content {
        background-color: #f9f9f9;
        padding: 15px;
        border-radius: 4px;
        border-left: 4px solid #2196F3;
    }
    
    .admin-form .form-group {
        margin-bottom: 20px;
    }
    
    .admin-form label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
    }
    
    .admin-form input[type="text"],
    .admin-form textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-family: inherit;
        font-size: 14px;
    }
    
    .admin-form textarea {
        resize: vertical;
    }
    
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
    }
</style>

<?php
// Incluir rodapé administrativo
require_once '../includes/admin_footer.php';
?>
