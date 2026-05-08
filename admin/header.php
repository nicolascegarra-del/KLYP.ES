<?php
require_once __DIR__ . '/security.php';
session_secure_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$paginaActual = basename($_SERVER['PHP_SELF']);
$nombreUsuario = $_SESSION['usuario_nombre'] ?? 'Admin';
$inicialUsuario = strtoupper(mb_substr($nombreUsuario, 0, 1));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klyp Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>

<div class="sidebar">
    <div class="sidebar-logo">
        <img src="../assets/img/logo_white.png" alt="Klyp">
    </div>

    <div class="sidebar-nav">
        <div class="menu-label">Web pública</div>
        <a href="index.php" class="menu-item <?php echo $paginaActual === 'index.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> Resumen
        </a>
        <a href="apps.php" class="menu-item <?php echo $paginaActual === 'apps.php' ? 'active' : ''; ?>">
            <i class="fas fa-th-large"></i> Aplicaciones
        </a>
        <a href="blog.php" class="menu-item <?php echo $paginaActual === 'blog.php' ? 'active' : ''; ?>">
            <i class="fas fa-newspaper"></i> Blog y Novedades
        </a>
        <a href="web_config.php" class="menu-item <?php echo $paginaActual === 'web_config.php' ? 'active' : ''; ?>">
            <i class="fas fa-sliders-h"></i> Configuración
        </a>

        <div class="menu-label">CRM Klyp</div>
        <a href="clientes.php" class="menu-item <?php echo in_array($paginaActual, ['clientes.php','cliente_editar.php']) ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> Clientes
        </a>
        <a href="suscripciones.php" class="menu-item <?php echo in_array($paginaActual, ['suscripciones.php','suscripcion_editar.php','suscripcion_nueva.php']) ? 'active' : ''; ?>">
            <i class="fas fa-sync-alt"></i> Suscripciones
        </a>

        <div class="menu-label">Sistema</div>
        <a href="usuarios.php" class="menu-item <?php echo in_array($paginaActual, ['usuarios.php','editar_usuario.php']) ? 'active' : ''; ?>">
            <i class="fas fa-user-shield"></i> Administradores
        </a>
        <a href="gestion_config.php" class="menu-item <?php echo $paginaActual === 'gestion_config.php' ? 'active' : ''; ?>">
            <i class="fas fa-file-invoice"></i> Datos Fiscales
        </a>
    </div>

    <div class="user-profile">
        <div class="user-avatar"><?php echo $inicialUsuario; ?></div>
        <div class="user-info">
            <div class="user-name"><?php echo htmlspecialchars($nombreUsuario); ?></div>
            <a href="logout.php">Cerrar sesión</a>
        </div>
    </div>
</div>

<div class="main-content">
