<?php 
require 'header.php'; 
require '../db.php'; 

// Contadores para el dashboard
$total_apps = 0;
$total_admins = 0;
$total_blog = 0; // Nuevo contador para el blog

try {
    $total_apps = $pdo->query("SELECT COUNT(*) FROM aplicaciones")->fetchColumn();
    // CORRECCIÓN: Contamos desde 'usuarios_admin'
    $total_admins = $pdo->query("SELECT COUNT(*) FROM usuarios_admin")->fetchColumn();
    // Contamos los posts del blog
    $total_blog = $pdo->query("SELECT COUNT(*) FROM blog")->fetchColumn();
} catch (Exception $e) { /* Silencio si falla */ }
?>

<div class="header">
    <div>
        <h1 class="page-title">Hola, <?php echo $nombreUsuario; ?>.</h1>
        <p style="color:#8898aa; margin:5px 0 0;">Bienvenido a tu panel de gestión.</p>
    </div>
    <div style="color: var(--text-gray); font-weight: 500;">
        <?php echo date("d/m/Y"); ?>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
    
    <div class="card" style="display:flex; align-items:center; justify-content:space-between;">
        <div>
            <h2 style="margin:0; font-size:2.5rem; color:var(--klyp-dark)"><?php echo $total_apps; ?></h2>
            <p style="margin:0; color:var(--text-gray)">Aplicaciones Activas</p>
        </div>
        <div style="font-size:2.5rem; color:var(--klyp-blue); opacity:0.2;">
            <i class="fas fa-layer-group"></i>
        </div>
    </div>

    <div class="card" style="display:flex; align-items:center; justify-content:space-between;">
        <div>
            <h2 style="margin:0; font-size:2.5rem; color:var(--klyp-dark)"><?php echo $total_blog; ?></h2>
            <p style="margin:0; color:var(--text-gray)">Artículos en Blog</p>
        </div>
        <div style="font-size:2.5rem; color:#e67e22; opacity:0.2;">
            <i class="fas fa-newspaper"></i>
        </div>
    </div>

    <div class="card" style="display:flex; align-items:center; justify-content:space-between;">
        <div>
            <h2 style="margin:0; font-size:2.5rem; color:var(--klyp-dark)"><?php echo $total_admins; ?></h2>
            <p style="margin:0; color:var(--text-gray)">Administradores</p>
        </div>
        <div style="font-size:2.5rem; color:#8e44ad; opacity:0.2;">
            <i class="fas fa-users-cog"></i>
        </div>
    </div>

    <div class="card" style="display:flex; align-items:center; justify-content:space-between;">
        <div>
            <h2 style="margin:0; font-size:1.5rem; color:#2ecc71">ONLINE</h2>
            <p style="margin:0; color:var(--text-gray)">Estado Web</p>
        </div>
        <div style="font-size:2.5rem; color:#2ecc71; opacity:0.2;">
            <i class="fas fa-check-circle"></i>
        </div>
    </div>

</div>

<div class="card" style="margin-top:20px;">
    <h3>Accesos Rápidos</h3>
    <p style="color:var(--text-gray)">Utiliza el menú lateral para gestionar las Apps de la web pública, configurar las redes sociales, el blog o crear nuevos administradores.</p>
    <br>
    <a href="apps.php" class="btn">Gestionar Apps</a>
    <a href="blog.php" class="btn" style="background:#e67e22; margin-left:10px;">Gestionar Blog</a>
    <a href="web_config.php" class="btn" style="background:#8898aa; margin-left:10px;">Configuración</a>
</div>

</div> 
</body>
</html>