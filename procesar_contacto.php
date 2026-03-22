<?php
session_start();

// Cabeceras de seguridad
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');

// Solo acepta POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Verificar token CSRF
$token_recibido = $_POST['_token'] ?? '';
$token_sesion   = $_SESSION['csrf_contact'] ?? '';
if (!$token_sesion || !hash_equals($token_sesion, $token_recibido)) {
    $_SESSION['contacto_error'] = 'Error de seguridad. Por favor recarga la página e inténtalo de nuevo.';
    header('Location: index.php#contacto');
    exit;
}
unset($_SESSION['csrf_contact']); // token de un solo uso

// Validar campos
$nombre  = trim($_POST['nombre']  ?? '');
$email   = trim($_POST['email']   ?? '');
$mensaje = trim($_POST['mensaje'] ?? '');

$errores = [];
if (strlen($nombre) < 2)       $errores[] = 'El nombre es demasiado corto.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = 'El email no es válido.';
if (strlen($mensaje) < 10)     $errores[] = 'El mensaje debe tener al menos 10 caracteres.';

if (!empty($errores)) {
    $_SESSION['contacto_error'] = implode(' ', $errores);
    header('Location: index.php#contacto');
    exit;
}

// Obtener email destino desde configuración
require 'db.php';
$config       = $pdo->query("SELECT email_contacto FROM configuracion WHERE id=1")->fetch();
$email_destino = $config['email_contacto'] ?? '';

if (!$email_destino || !filter_var($email_destino, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['contacto_error'] = 'El formulario de contacto no está configurado. Por favor contáctanos directamente.';
    header('Location: index.php#contacto');
    exit;
}

// Enviar email
$asunto  = 'Nuevo mensaje de contacto desde KLYP.es';
$cuerpo  = "Has recibido un nuevo mensaje desde el formulario de contacto de tu web.\n\n";
$cuerpo .= "Nombre:  " . $nombre  . "\n";
$cuerpo .= "Email:   " . $email   . "\n\n";
$cuerpo .= "Mensaje:\n" . $mensaje . "\n";

$headers  = "From: no-reply@klyp.es\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "X-Mailer: PHP/" . PHP_VERSION;

if (mail($email_destino, $asunto, $cuerpo, $headers)) {
    $_SESSION['contacto_ok'] = '¡Mensaje enviado correctamente! Te responderemos lo antes posible.';
} else {
    $_SESSION['contacto_error'] = 'No se pudo enviar el mensaje. Por favor inténtalo de nuevo o contáctanos directamente.';
}

header('Location: index.php#contacto');
exit;
