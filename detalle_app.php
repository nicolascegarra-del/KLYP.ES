<?php
require 'db.php';
$config = $pdo->query('SELECT * FROM configuracion WHERE id = 1')->fetch();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: index.php'); exit; }

$stmt = $pdo->prepare('SELECT * FROM aplicaciones WHERE id = ?');
$stmt->execute([$id]);
$app = $stmt->fetch();
if (!$app) { header('Location: index.php'); exit; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($app['titulo']); ?> | KLYP</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

<!-- ====== NAVBAR ====== -->
<nav class="navbar scrolled" id="navbar">
    <a href="index.php" class="navbar-logo">
        <img src="assets/img/logo_white.png" alt="Klyp">
    </a>
    <button class="nav-toggle" id="nav-toggle" aria-label="Menú">
        <span></span><span></span><span></span>
    </button>
    <ul class="nav-links" id="nav-links">
        <li><a href="index.php#nosotros">Quiénes somos</a></li>
        <li><a href="index.php#que-hacemos">Qué hacemos</a></li>
        <li><a href="blog.php">Blog</a></li>
        <li><a href="index.php#contacto">Contacto</a></li>
        <li><a href="#" class="btn-client">Acceso Cliente</a></li>
    </ul>
</nav>

<!-- ====== HEADER ====== -->
<div class="app-page-header">
    <h1>App <span class="accent"><?php echo htmlspecialchars($app['titulo']); ?></span></h1>
</div>

<!-- ====== APP DETAIL ====== -->
<div class="app-detail-wrap">
    <div class="app-detail-card">
        <div class="app-detail-icon">
            <i class="fas <?php echo htmlspecialchars($app['icono']); ?>"></i>
        </div>
        <h2><?php echo htmlspecialchars($app['titulo']); ?></h2>
        <p class="subtitle"><?php echo htmlspecialchars($app['descripcion']); ?></p>

        <div class="app-detail-content">
            <?php echo !empty($app['contenido_extendido'])
                ? htmlspecialchars($app['contenido_extendido'])
                : 'Próximamente más información detallada sobre esta aplicación.'; ?>
        </div>

        <div style="display:flex; gap:16px; justify-content:center; flex-wrap:wrap;">
            <a href="index.php#apps-directas" class="btn-outline">
                <i class="fas fa-arrow-left"></i> Volver a las apps
            </a>
            <?php if (!empty($app['enlace']) && $app['enlace'] !== '#'): ?>
            <a href="<?php echo htmlspecialchars($app['enlace']); ?>" class="btn-primary" target="_blank" rel="noopener">
                Acceder a la app <i class="fas fa-external-link-alt"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ====== FOOTER ====== -->
<footer>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <img src="assets/img/logo_white.png" alt="Klyp">
                <p>Desarrollo de software, inteligencia artificial y transformación digital.</p>
            </div>
            <div class="footer-col">
                <h5>Navegación</h5>
                <ul>
                    <li><a href="index.php#nosotros">Quiénes somos</a></li>
                    <li><a href="index.php#que-hacemos">Qué hacemos</a></li>
                    <li><a href="blog.php">Blog</a></li>
                    <li><a href="index.php#contacto">Contacto</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h5>Síguenos</h5>
                <div class="social-links">
                    <?php if (!empty($config['twitter'])): ?>
                        <a href="<?php echo htmlspecialchars($config['twitter']); ?>" target="_blank"><i class="fab fa-twitter"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($config['instagram'])): ?>
                        <a href="<?php echo htmlspecialchars($config['instagram']); ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($config['linkedin'])): ?>
                        <a href="<?php echo htmlspecialchars($config['linkedin']); ?>" target="_blank"><i class="fab fa-linkedin"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($config['facebook'])): ?>
                        <a href="<?php echo htmlspecialchars($config['facebook']); ?>" target="_blank"><i class="fab fa-facebook"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; <?php echo date('Y'); ?> KLYP. Todos los derechos reservados.</span>
            <a href="admin/index.php"><i class="fas fa-cog" style="margin-right:5px;"></i> Panel Admin</a>
        </div>
    </div>
</footer>

<script>
const toggle = document.getElementById('nav-toggle');
const navLinks = document.getElementById('nav-links');
toggle.addEventListener('click', () => navLinks.classList.toggle('open'));
navLinks.querySelectorAll('a').forEach(a => a.addEventListener('click', () => navLinks.classList.remove('open')));
</script>

</body>
</html>
