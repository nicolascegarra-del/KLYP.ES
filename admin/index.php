<?php
require 'header.php';
require '../db.php';

$total_apps   = 0;
$total_blog   = 0;
$total_admins = 0;
$total_clientes = 0;

try {
    $total_apps     = $pdo->query('SELECT COUNT(*) FROM aplicaciones')->fetchColumn();
    $total_blog     = $pdo->query('SELECT COUNT(*) FROM blog')->fetchColumn();
    $total_admins   = $pdo->query('SELECT COUNT(*) FROM usuarios_admin')->fetchColumn();
    $total_clientes = $pdo->query('SELECT COUNT(*) FROM clientes')->fetchColumn();
} catch (Exception $e) {}
?>

<div class="header">
    <div>
        <h1 class="page-title">Hola, <?php echo htmlspecialchars($nombreUsuario); ?> 👋</h1>
        <p style="color:var(--gray); margin-top:4px; font-size:0.9rem; font-weight:600;">
            <?php echo date('l, d \d\e F \d\e Y'); ?> · Panel de gestión Klyp
        </p>
    </div>
    <a href="../index.php" target="_blank" class="btn" style="background:var(--pale); color:var(--accent);">
        <i class="fas fa-external-link-alt"></i> Ver web
    </a>
</div>

<!-- Stat cards -->
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:16px; margin-bottom:24px;">
    <div class="stat-card">
        <div>
            <div class="stat-card-value"><?php echo $total_apps; ?></div>
            <div class="stat-card-label">Aplicaciones</div>
        </div>
        <div class="stat-card-icon" style="background:#EFF6FF; color:#2E6DB4;">
            <i class="fas fa-th-large"></i>
        </div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-card-value"><?php echo $total_blog; ?></div>
            <div class="stat-card-label">Artículos en blog</div>
        </div>
        <div class="stat-card-icon" style="background:#FFF7ED; color:#D97706;">
            <i class="fas fa-newspaper"></i>
        </div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-card-value"><?php echo $total_clientes; ?></div>
            <div class="stat-card-label">Clientes</div>
        </div>
        <div class="stat-card-icon" style="background:#F0FDF4; color:#16A34A;">
            <i class="fas fa-users"></i>
        </div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-card-value"><?php echo $total_admins; ?></div>
            <div class="stat-card-label">Administradores</div>
        </div>
        <div class="stat-card-icon" style="background:#FAF5FF; color:#7C3AED;">
            <i class="fas fa-user-shield"></i>
        </div>
    </div>
</div>

<!-- Quick access -->
<div class="card">
    <h3>Accesos rápidos</h3>
    <p style="color:var(--gray); font-size:0.9rem; margin-bottom:20px; font-weight:600;">
        Gestiona el contenido de la web pública, los clientes y la configuración desde el menú lateral o los accesos directos.
    </p>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="apps.php" class="btn"><i class="fas fa-th-large"></i> Apps</a>
        <a href="blog.php" class="btn" style="background:#FFF7ED; color:#D97706;"><i class="fas fa-newspaper"></i> Blog</a>
        <a href="clientes.php" class="btn" style="background:#F0FDF4; color:#16A34A;"><i class="fas fa-users"></i> Clientes</a>
        <a href="suscripciones.php" class="btn" style="background:#FAF5FF; color:#7C3AED;"><i class="fas fa-sync-alt"></i> Suscripciones</a>
        <a href="web_config.php" class="btn" style="background:var(--pale); color:var(--accent);"><i class="fas fa-sliders-h"></i> Configuración</a>
    </div>
</div>

</div></body></html>
