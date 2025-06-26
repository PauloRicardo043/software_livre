<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
        $stmt->execute([$id]);
        setMensagem('sucesso', 'Cliente excluído com sucesso.');
    } catch (PDOException $e) {
        setMensagem('erro', 'Erro ao excluir cliente: ' . $e->getMessage());
    }
}

header('Location: clientes.php');
exit;
?>
