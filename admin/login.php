<?php
// Página de login do administrador
$titulo_pagina = 'Login Administrativo';
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

// Iniciar sessão
iniciarSessao();

// Verificar se já está logado
if (estaLogado()) {
    header('Location: dashboard.php');
    exit;
}

// Processar o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar dados
    $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
    $senha = isset($_POST['senha']) ? $_POST['senha'] : '';
    
    $erros = [];
    
    // Validações básicas
    if (empty($usuario)) {
        $erros[] = 'O usuário é obrigatório.';
    }
    
    if (empty($senha)) {
        $erros[] = 'A senha é obrigatória.';
    }
    
    // Se não houver erros, verificar credenciais
    if (empty($erros)) {
        $db = Database::getInstance();
        $usuario_db = $db->fetchOne("SELECT * FROM usuarios WHERE usuario = ?", [$usuario]);
        
        // Debug para verificar o que está sendo retornado do banco
        // debug($usuario_db);
        
        if ($usuario_db && verificarSenha($senha, $usuario_db['senha'])) {
            // Credenciais válidas, criar sessão
            $_SESSION['usuario_id'] = $usuario_db['id'];
            $_SESSION['usuario_nome'] = $usuario_db['nome'];
            $_SESSION['usuario_nivel'] = $usuario_db['nivel_acesso'];
            $_SESSION['usuario_email'] = $usuario_db['email'];


            
            // Atualizar último acesso
            $db->update('usuarios', [
                'ultimo_acesso' => date('Y-m-d H:i:s')
            ], 'id = ?', [$usuario_db['id']]);
            
            // Registrar log
            registrarLog('login', 'Login realizado com sucesso', $usuario_db['id']);
            
            // Redirecionar para o dashboard
            header('Location: dashboard.php');
            exit;
        } else {
            // Credenciais inválidas
            $erros[] = 'Usuário ou senha incorretos.';
            
            // Registrar tentativa de login inválida
            registrarLog('login_falha', 'Tentativa de login falhou para o usuário: ' . $usuario);
        }
    }
    
    // Exibir erros
    if (!empty($erros)) {
        setMensagem('error', implode('<br>', $erros));
    }
}

// Obter mensagem flash, se houver
$mensagem = getMensagem();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo_pagina; ?> - IDEA Service</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-logo">
            <img src="<?php echo SITE_URL; ?>/images/logo.png" alt="IDEA Service">
        </div>
        
        <?php if ($mensagem): ?>
        <div class="mensagem-flash mensagem-<?php echo $mensagem['tipo']; ?>">
            <?php echo $mensagem['texto']; ?>
            <span class="fechar-mensagem">&times;</span>
        </div>
        <?php endif; ?>
        
        <div class="login-form">
            <h2>Login Administrativo</h2>
            <form action="login.php" method="post">
                <div class="form-group">
                    <label for="usuario">Usuário</label>
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="usuario" name="usuario" required value="<?php echo isset($_POST['usuario']) ? sanitizar($_POST['usuario']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="senha" name="senha" required autocomplete="off" autocapitalize="off" spellcheck="false">
                        <span class="toggle-password" onclick="togglePasswordVisibility()" style="cursor:pointer; position:absolute; right:10px; top:50%; transform:translateY(-50%);">
                            <i id="toggle-icon" class="fas fa-eye-slash"></i>
                        </span>
                        <span class="toggle-password" onclick="togglePasswordVisibility()" style="cursor:pointer; position:absolute; right:10px; top:50%; transform:translateY(-50%);">
                            <i id="toggle-icon" class="fas fa-eye-slash"></i>
                        </span>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-login">Entrar</button>
            </form>
            
            <div class="login-footer">
                <p>IDEA Service &copy; <?php echo date('Y'); ?></p>
                <p><a href="<?php echo SITE_URL; ?>">Voltar para o site</a></p>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fechar mensagem flash
            const mensagemFlash = document.querySelector('.mensagem-flash');
            const fecharMensagem = document.querySelector('.fechar-mensagem');
            
            if (mensagemFlash && fecharMensagem) {
                fecharMensagem.addEventListener('click', function() {
                    mensagemFlash.style.display = 'none';
                });
                
                // Auto-fechar após 5 segundos
                setTimeout(function() {
                    mensagemFlash.style.display = 'none';
                }, 5000);
            }
        });
    </script>

    <script>
        function togglePasswordVisibility() {
            const senhaInput = document.getElementById('senha');
            const toggleIcon = document.getElementById('toggle-icon');
            if (senhaInput.type === 'password') {
                senhaInput.type = 'text';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            } else {
                senhaInput.type = 'password';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>
        
</html>
