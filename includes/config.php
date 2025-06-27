<?php
// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'ppiuser');
define('DB_PASS', 'ppi123');
define('DB_NAME', 'idea_service_db');

// Configurações do site
define('SITE_NAME', 'IDEA Service');
define('SITE_URL', 'http://192.168.251.7/idea_service/');
define('ADMIN_EMAIL', 'admin@ideaservices.com.br');

// Configurações de sessão
define('SESSION_NAME', 'idea_service_session');
define('SESSION_LIFETIME', 7200); // 2 horas em segundos

// Configurações de segurança
define('HASH_COST', 10); // Custo para o algoritmo de hash de senha

// Configurações de timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurações de exibição de erros (desativar em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configurações de upload de arquivos
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/idea_service/uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB em bytes
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']);
