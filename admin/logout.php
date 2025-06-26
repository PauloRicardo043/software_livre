<?php
// Página de logout
require_once '../includes/config.php';
require_once '../includes/db.php';           // ✅ necessário para Database
require_once '../includes/functions.php';

// Iniciar sessão
iniciarSessao();

// Registrar log de logout se o usuário estiver logado
if (estaLogado()) {
    registrarLog('logout', 'Logout realizado com sucesso', $_SESSION['usuario_id']);
}

// Destruir a sessão
session_unset();
session_destroy();

// Redirecionar para a página inicial
setMensagem('success', 'Você saiu do sistema com sucesso.');
header('Location: ' . SITE_URL . '/index.php');
exit;
?>
