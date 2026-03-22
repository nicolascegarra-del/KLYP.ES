<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// ── Cabeceras de seguridad HTTP ──────────────────────────────────────────────
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; img-src 'self' data:;");

// ── CSRF y rate limiting ─────────────────────────────────────────────────────
require_once __DIR__ . '/csrf.php';

// ── Validación de sesión ─────────────────────────────────────────────────────
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$paginaActual = basename($_SERVER['PHP_SELF']);
$nombreUsuario = $_SESSION['usuario_nombre'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Klyp Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <div class="sidebar">
        <div class="brand" style="text-align: center; margin-bottom: 30px;">
            <img src="../assets/img/logo_white.png" alt="Klyp Logo" style="max-width: 50%; height: auto;">
        </div>

        <div class="menu-label">Administración Web</div>
        <a href="index.php" class="menu-item <?php echo ($paginaActual == 'index.php') ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> Resumen
        </a>
        <a href="apps.php" class="menu-item <?php echo ($paginaActual == 'apps.php') ? 'active' : ''; ?>">
            <i class="fas fa-th-large"></i> Aplicaciones
        </a>
        <a href="blog.php" class="menu-item <?php echo ($paginaActual == 'blog.php') ? 'active' : ''; ?>">
            <i class="fas fa-newspaper"></i> Blog y Novedades
        </a>
        <a href="web_config.php" class="menu-item <?php echo ($paginaActual == 'web_config.php') ? 'active' : ''; ?>">
            <i class="fas fa-sliders-h"></i> Configuración
        </a>

        <div class="menu-label" style="margin-top: 20px;">Sistema</div>
        <a href="usuarios.php" class="menu-item <?php echo ($paginaActual == 'usuarios.php') ? 'active' : ''; ?>">
            <i class="fas fa-user-shield"></i> Administradores
        </a>

        <div class="user-profile">
            <div class="user-avatar"><?php echo strtoupper(substr($nombreUsuario, 0, 1)); ?></div>
            <div style="font-size: 0.9rem;">
                <div><?php echo htmlspecialchars($nombreUsuario); ?></div>
                <a href="logout.php" style="font-size: 0.75rem; color: var(--klyp-blue);">Cerrar Sesión</a>
            </div>
        </div>
    </div>

    <div class="main-content">
