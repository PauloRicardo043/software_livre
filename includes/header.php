<?php
// Arquivo de cabeçalho para todas as páginas do site
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db.php';

// Iniciar sessão
iniciarSessao();

// Obter mensagem flash, se houver
$mensagem = getMensagem();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($titulo_pagina) ? $titulo_pagina . ' - ' . SITE_NAME : SITE_NAME; ?></title>
   <link rel="stylesheet" href="/idea_service/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <?php if (isset($css_adicional) && is_array($css_adicional)): ?>
        <?php foreach ($css_adicional as $css): ?>
            <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-container">
            <div class="logo">
                <a href="<?php echo SITE_URL; ?>/index.php"><img src="<?php echo SITE_URL; ?>/images/logo.png" alt="IDEA Service"></a>
            </div>
            <div class="menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <nav>
                <ul>
                    <li<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? ' class="active"' : ''; ?>><a href="<?php echo SITE_URL; ?>/index.php">Início</a></li>
                    <li<?php echo basename($_SERVER['PHP_SELF']) == 'sobre.php' ? ' class="active"' : ''; ?>><a href="<?php echo SITE_URL; ?>/sobre.php">Sobre</a></li>
                    <li<?php echo basename($_SERVER['PHP_SELF']) == 'servicos.php' ? ' class="active"' : ''; ?>><a href="<?php echo SITE_URL; ?>/servicos.php">Serviços</a></li>
                    <li<?php echo basename($_SERVER['PHP_SELF']) == 'contato.php' ? ' class="active"' : ''; ?>><a href="<?php echo SITE_URL; ?>/contato.php">Contato</a></li>
                    <li<?php echo strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? ' class="active"' : ''; ?>><a href="<?php echo SITE_URL; ?>/admin/login.php">Área do Administrador</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <?php if ($mensagem): ?>
    <!-- Mensagem Flash -->
    <div class="mensagem-flash mensagem-<?php echo $mensagem['tipo']; ?>">
        <div class="container">
            <?php echo $mensagem['texto']; ?>
            <span class="fechar-mensagem">&times;</span>
        </div>
    </div>
    <?php endif; ?>
