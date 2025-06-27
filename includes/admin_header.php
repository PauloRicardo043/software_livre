<?php
// Arquivo de cabeçalho para páginas administrativas
// Iniciar sessão se não estiver ativa
iniciarSessao();

// Verificar se o usuário está logado
if (!estaLogado() && basename($_SERVER['PHP_SELF']) != 'login.php') {
    header('Location: ' . SITE_URL . '/admin/login.php');
    exit;
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
    <?php if (isset($css_adicional) && is_array($css_adicional)): ?>
        <?php foreach ($css_adicional as $css): ?>
            <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container header-container">
            <div class="logo">
                <a href="<?php echo SITE_URL; ?>">
                    <img src="<?php echo SITE_URL; ?>/images/logo.png" alt="IDEA Service">
                </a>
            </div>
            <nav>
                <ul>
                    <li <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'class="active"' : ''; ?>>
                        <a href="<?php echo SITE_URL; ?>/admin/dashboard.php">Dashboard</a>
                    </li>
                    <li <?php echo strpos(basename($_SERVER['PHP_SELF']), 'orcamento') !== false ? 'class="active"' : ''; ?>>
                        <a href="<?php echo SITE_URL; ?>/admin/orcamentos-pendentes.php">Orçamentos</a>
                    </li>
                    <li <?php echo strpos(basename($_SERVER['PHP_SELF']), 'contato') !== false ? 'class="active"' : ''; ?>>
                        <a href="<?php echo SITE_URL; ?>/admin/contatos.php">Contatos</a>
                    </li>
                    <li <?php echo strpos(basename($_SERVER['PHP_SELF']), 'cliente') !== false ? 'class="active"' : ''; ?>>
                        <a href="<?php echo SITE_URL; ?>/admin/clientes.php">Clientes</a>
                    </li>
                    <li <?php echo basename($_SERVER['PHP_SELF']) == 'configuracoes.php' ? 'class="active"' : ''; ?>>
                        <a href="<?php echo SITE_URL; ?>/admin/configuracoes.php">Configurações</a>
                    </li>
                    <li>
                        <a href="<?php echo SITE_URL; ?>/admin/logout.php">Sair</a>
                    </li>
                </ul>
            </nav>
            <div class="menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </header>

    <?php if ($mensagem): ?>
    <div class="container">
        <div class="mensagem-flash mensagem-<?php echo $mensagem['tipo']; ?>">
            <?php echo $mensagem['texto']; ?>
            <span class="fechar-mensagem">&times;</span>
        </div>
    </div>
    <?php endif; ?>
