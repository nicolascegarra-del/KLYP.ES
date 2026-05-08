<?php
require_once 'security.php';
session_secure_start();
require_once '../db.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Solo acepta POST con token CSRF válido
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: suscripciones.php');
    exit;
}

csrf_verify();

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id > 0) {
    $pdo->prepare('DELETE FROM suscripciones WHERE id = ?')->execute([$id]);
    header('Location: suscripciones.php?mensaje=eliminado');
} else {
    header('Location: suscripciones.php');
}
exit;
