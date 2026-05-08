<?php
require 'db.php';
$config = $pdo->query('SELECT * FROM configuracion WHERE id = 1')->fetch();

$ver_detalle = false;
$noticia = null;

if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM blog WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $noticia = $stmt->fetch();
    if ($noticia) $ver_detalle = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KLYP | <?php echo $ver_detalle ? htmlspecialchars($noticia['titulo']) : 'Blog y Novedades'; ?></title>
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
        <li><a href="blog.php" style="color:#60a5fa; font-weight:800;">Blog</a></li>
        <li><a href="index.php#contacto">Contacto</a></li>
        <li><a href="#" class="btn-client">Acceso Cliente</a></li>
    </ul>
</nav>

<!-- ====== HEADER ====== -->
<div class="blog-page-header">
    <?php if ($ver_detalle): ?>
        <h1 class="blog-detail-title"><?php echo htmlspecialchars($noticia['titulo']); ?></h1>
        <div class="blog-detail-meta">
            <span class="tag"><i class="fas fa-tag"></i> <?php echo htmlspecialchars($noticia['tipo']); ?></span>
            <span class="date">
                <i class="far fa-calendar-alt"></i>
                Publicado el <?php echo date('d/m/Y', strtotime($noticia['fecha'])); ?>
            </span>
        </div>
    <?php else: ?>
        <h1>Blog y <span class="accent">Novedades</span></h1>
        <p>Mantente al día con las últimas actualizaciones de nuestras aplicaciones y las noticias corporativas del ecosistema Klyp.</p>
    <?php endif; ?>
</div>

<!-- ====== CONTENT ====== -->
<?php if ($ver_detalle): ?>

    <div class="blog-post-wrap">
        <div class="blog-post-card">
            <?php if ($noticia['imagen']): ?>
                <img class="blog-post-img" src="<?php echo htmlspecialchars($noticia['imagen']); ?>" alt="<?php echo htmlspecialchars($noticia['titulo']); ?>">
            <?php endif; ?>
            <div class="blog-post-body">
                <div class="blog-text"><?php echo htmlspecialchars($noticia['texto']); ?></div>
                <div class="blog-back">
                    <a href="blog.php" class="btn-outline">
                        <i class="fas fa-arrow-left"></i> Volver al listado
                    </a>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>

    <div class="blog-list-wrap">
        <?php
        $noticias = $pdo->query('SELECT * FROM blog ORDER BY fecha DESC')->fetchAll();
        if (!empty($noticias)):
            foreach ($noticias as $item):
        ?>
        <a href="blog.php?id=<?php echo $item['id']; ?>" class="blog-list-item">
            <div class="blog-list-thumb">
                <img src="<?php echo htmlspecialchars($item['imagen']); ?>" alt="">
            </div>
            <div class="blog-list-content">
                <span class="blog-list-tag"><?php echo htmlspecialchars($item['tipo']); ?></span>
                <h2><?php echo htmlspecialchars($item['titulo']); ?></h2>
                <p><?php echo htmlspecialchars(mb_strimwidth($item['texto'], 0, 180, '…')); ?></p>
                <div class="blog-list-footer">
                    <span class="blog-list-date">
                        <i class="far fa-calendar-alt"></i>
                        <?php echo date('d/m/Y', strtotime($item['fecha'])); ?>
                    </span>
                    <span class="blog-list-cta">
                        Leer artículo <i class="fas fa-arrow-right"></i>
                    </span>
                </div>
            </div>
        </a>
        <?php
            endforeach;
        else:
        ?>
        <div class="blog-empty">
            <i class="fas fa-newspaper"></i>
            <h3>Aún no hay publicaciones</h3>
            <p>Pronto compartiremos las últimas novedades del ecosistema Klyp.</p>
        </div>
        <?php endif; ?>
    </div>

<?php endif; ?>

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
