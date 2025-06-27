<?php
// Arquivo de funções utilitárias para o sistema

// Iniciar sessão se não estiver ativa
function iniciarSessao() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

// Verificar se o usuário está logado
function estaLogado() {
    iniciarSessao();
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

// Verificar login e redirecionar se não estiver logado
function verificarLogin() {
    if (!estaLogado()) {
        setMensagem('error', 'Você precisa estar logado para acessar esta página.');
        header('Location: ' . SITE_URL . '/admin/login.php');
        exit;
    }
}

// Sanitizar dados de entrada
function sanitizar($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = sanitizar($value);
        }
        return $data;
    }
    
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Gerar hash de senha
function gerarHash($senha) {
    return password_hash($senha, PASSWORD_DEFAULT);
}

// Verificar senha
function verificarSenha($senha, $hash) {
    // Verifica se o hash está no formato antigo (MD5)
    if (strlen($hash) == 32 && ctype_xdigit($hash)) {
        return md5($senha) === $hash;
    }
    
    // Verifica com password_verify para hashes modernos
    return password_verify($senha, $hash);
}

// Gerar token CSRF
function gerarTokenCSRF() {
    iniciarSessao();
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verificar token CSRF
function verificarTokenCSRF($token) {
    iniciarSessao();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Definir mensagem flash
function setMensagem($tipo, $texto) {
    iniciarSessao();
    $_SESSION['mensagem'] = [
        'tipo' => $tipo,
        'texto' => $texto
    ];
}

// Obter mensagem flash
function getMensagem() {
    iniciarSessao();
    if (isset($_SESSION['mensagem'])) {
        $mensagem = $_SESSION['mensagem'];
        unset($_SESSION['mensagem']);
        return $mensagem;
    }
    return null;
}

// Formatar data
function formatarData($data) {
    if (empty($data)) return '';
    $timestamp = strtotime($data);
    return date('d/m/Y H:i', $timestamp);
}

// Formatar moeda
function formatarMoeda($valor) {
    if (empty($valor)) return 'R$ 0,00';
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

// Formatar status de orçamento
function formatarStatusOrcamento($status) {
    switch ($status) {
        case 'pendente':
            return 'Pendente';
        case 'respondido':
            return 'Respondido';
        case 'expirado':
            return 'Expirado';
        default:
            return ucfirst($status);
    }
}

// Registrar log de atividade
function registrarLog($tipo, $descricao, $usuario_id = null) {
    if (is_null($usuario_id) && estaLogado()) {
        $usuario_id = $_SESSION['usuario_id'];
    }
    
    $db = Database::getInstance();
    $db->insert('logs', [
        'tipo' => $tipo,
        'descricao' => $descricao,
        'usuario_id' => $usuario_id,
        'ip' => $_SERVER['REMOTE_ADDR'],
        'data_hora' => date('Y-m-d H:i:s')
    ]);
}

// Função para debug
function debug($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

// Função para enviar e-mail simples em HTML
function enviarEmail($destinatario, $assunto, $mensagem, $remetente = null) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: " . ($remetente ?? "no-reply@seudominio.com") . "\r\n";
    return mail($destinatario, $assunto, $mensagem, $headers);
}