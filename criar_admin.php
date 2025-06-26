<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

$senha = password_hash('admin123', PASSWORD_DEFAULT);

$db = Database::getInstance();
$db->insert('usuarios', [
    'nome' => 'Administrador',
    'usuario' => 'admin',
    'email' => 'admin@ideaservices.com.br',
    'senha' => $senha,
    'nivel_acesso' => 'admin'
]);

echo "Usuário admin criado com sucesso!";
