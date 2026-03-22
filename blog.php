<?php
require 'db.php';

// Cabeceras de seguridad
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');

$config = $pdo->query("SELECT * FROM configuracion WHERE id = 1")->fetch();

$ver_detalle = false;
$noticia     = null;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM blog WHERE id = ?");
    $stmt->execute([(int)$_GET['id']]);
    $noticia = $stmt->fetch();
    if ($noticia) $ver_detalle = true;
}

// SEO
$og_url      = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'klyp.es');
$page_title  = $ver_detalle ? htmlspecialchars($noticia['titulo']) . ' | KLYP' : 'Blog y Novedades | KLYP';
$meta_desc   = $ver_detalle
    ? htmlspecialchars(mb_strimwidth(strip_tags($noticia['texto']), 0, 155, '...'))
    : 'Últimas novedades y actualizaciones del ecosistema KLYP. Noticias corporativas y de nuestras aplicaciones.';
$canonical   = $og_url . '/blog.php' . ($ver_detalle ? '?id=' . (int)$_GET['id'] : '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>

    <!-- SEO -->
    <meta name="description" content="<?php echo $meta_desc; ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical); ?>">

    <!-- Open Graph -->
    <meta property="og:type"        content="<?php echo $ver_detalle ? 'article' : 'website'; ?>">
    <meta property="og:url"         content="<?php echo htmlspecialchars($canonical); ?>">
    <meta property="og:title"       content="<?php echo $page_title; ?>">
    <meta property="og:description" content="<?php echo $meta_desc; ?>">
    <?php if ($ver_detalle && $noticia['imagen']): ?>
    <meta property="og:image" content="<?php echo htmlspecialchars($og_url . '/' . $noticia['imagen']); ?>">
    <?php endif; ?>

    <!-- Twitter Card -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="<?php echo $page_title; ?>">
    <meta name="twitter:description" content="<?php echo $meta_desc; ?>">

    <?php if ($ver_detalle): ?>
    <!-- Schema.org Article -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Article",
        "headline": "<?php echo htmlspecialchars($noticia['titulo'], ENT_QUOTES); ?>",
        "datePublished": "<?php echo htmlspecialchars($noticia['fecha']); ?>",
        "publisher": { "@type": "Organization", "name": "KLYP", "url": "<?php echo $og_url; ?>" }
    }
    </script>
    <?php endif; ?>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        .blog-list-container { max-width:1000px; margin:-80px auto 80px; position:relative; z-index:10; display:flex; flex-direction:column; gap:40px; padding:0 20px; }
        .blog-list-item { display:flex; background:white; border-radius:20px; box-shadow:0 10px 30px rgba(0,0,0,0.08); overflow:hidden; transition:transform 0.3s ease,box-shadow 0.3s ease; text-decoration:none; color:inherit; }
        .blog-list-item:hover { transform:translateY(-5px); box-shadow:0 15px 40px rgba(0,0,0,0.12); }
        .blog-list-image { width:350px; flex-shrink:0; display:flex; align-items:center; justify-content:center; background-color:#fafbfc; padding:20px; }
        .blog-list-image img { width:100%; height:250px; object-fit:contain; transition:transform 0.5s ease; }
        .blog-list-item:hover .blog-list-image img { transform:scale(1.03); }
        .blog-list-content { padding:40px; display:flex; flex-direction:column; justify-content:center; flex:1; }
        .blog-list-btn-visual { display:inline-block; padding:12px 25px; font-size:0.95rem; font-weight:600; border-radius:8px; background-color:#00A8E8 !important; color:#fff !important; box-shadow:0 4px 15px rgba(0,168,232,0.3); }
        .blog-list-item:hover .blog-list-btn-visual { background-color:#008cc2 !important; }
        .blog-post-content { max-width:900px; margin:-80px auto 80px; background:white; padding:50px; border-radius:20px; box-shadow:0 15px 35px rgba(0,0,0,0.1); position:relative; z-index:10; }
        .blog-post-content img { width:100%; max-height:450px; object-fit:contain; background-color:#fafbfc; border-radius:12px; margin-bottom:40px; padding:20px; }
        .menu-toggle { display:none; color:white; font-size:2rem; cursor:pointer; padding:5px; }
        @media(max-width:768px){
            .blog-list-item{flex-direction:column;}
            .blog-list-image{width:100%;height:auto;padding:10px;}
            .blog-list-image img{height:200px;}
            .blog-list-content{padding:30px;}
            .blog-post-content{padding:30px 20px;}
            header{position:relative;display:flex !important;justify-content:space-between !important;align-items:center !important;flex-wrap:wrap;padding:15px 20px !important;}
            .menu-toggle{display:block !important;}
            nav{width:100%;}
            #nav-list{display:none !important;flex-direction:column;width:100%;background-color:#000c24 !important;position:absolute;top:100%;left:0;padding:10px 0 !important;margin:0 !important;box-shadow:0 10px 20px rgba(0,0,0,0.5);z-index:9999;}
            #nav-list.active{display:flex !important;}
            #nav-list li{margin:0 !important;padding:0 !important;text-align:center;}
            #nav-list li a{font-size:1.05rem !important;display:block !important;padding:10px 20px !important;line-height:1 !important;}
        }
    </style>
</head>
<body style="background-color:#f4f7fa;">

<div class="hero-wrapper" style="padding-bottom:150px;">
    <header style="padding:20px; display:flex; justify-content:space-between; align-items:center; position:relative;">
        <a href="index.php" style="display:flex; align-items:center; text-decoration:none;">
            <img src="assets/img/logo_white.png" alt="KLYP - Inicio" style="height:45px; cursor:pointer;">
        </a>
        <div class="menu-toggle" id="mobile-menu"><i class="fas fa-bars"></i></div>
        <nav>
            <ul id="nav-list">
                <li><a href="index.php#nosotros">¿Quiénes somos?</a></li>
                <li><a href="index.php#que-hacemos">¿Qué hacemos?</a></li>
                <li><a href="blog.php" style="color:var(--klyp-blue); font-weight:bold;">Blog</a></li>
                <li><a href="index.php#contacto">Contacto</a></li>
                <li><a href="#" class="btn-client">Acceso Cliente</a></li>
            </ul>
        </nav>
    </header>

    <section class="hero" style="padding:60px 20px; text-align:center;">
        <div style="max-width:800px; margin:0 auto;">
            <?php if ($ver_detalle): ?>
                <h1 style="font-size:2.5rem; line-height:1.3; margin-bottom:20px;"><?php echo htmlspecialchars($noticia['titulo']); ?></h1>
                <div style="display:inline-flex; align-items:center; gap:15px; flex-wrap:wrap; justify-content:center;">
                    <span style="background:rgba(0,168,232,0.2); color:#00d2ff; padding:6px 15px; border-radius:20px; font-weight:600; font-size:0.95rem;">
                        <i class="fas fa-tag"></i> <?php echo htmlspecialchars($noticia['tipo']); ?>
                    </span>
                    <span style="color:rgba(255,255,255,0.7); font-size:0.95rem;">
                        <i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($noticia['fecha'])); ?>
                    </span>
                </div>
            <?php else: ?>
                <h1 style="font-size:3rem; margin-bottom:15px;">Blog y <span class="highlight">Novedades</span></h1>
                <span class="subtitle" style="display:block; max-width:600px; margin:0 auto; line-height:1.6;">
                    Mantente al día con las últimas actualizaciones de nuestras aplicaciones y noticias corporativas de KLYP.
                </span>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php if ($ver_detalle): ?>
    <div class="blog-post-content">
        <?php if ($noticia['imagen']): ?>
            <img src="<?php echo htmlspecialchars($noticia['imagen']); ?>"
                 alt="<?php echo htmlspecialchars($noticia['titulo']); ?>">
        <?php endif; ?>
        <div style="font-size:1.1rem; line-height:1.8; color:#444; white-space:pre-wrap;">
            <?php echo htmlspecialchars($noticia['texto']); ?>
        </div>
        <div style="margin-top:50px; text-align:center; border-top:1px solid #eee; padding-top:40px;">
            <a href="blog.php" class="btn" style="background:transparent; border:2px solid #00A8E8; color:#00A8E8; padding:12px 30px; border-radius:50px; font-weight:600; text-decoration:none;">
                <i class="fas fa-arrow-left" style="margin-right:8px;"></i> Volver al listado
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="blog-list-container">
        <?php
        $noticias = $pdo->query("SELECT * FROM blog ORDER BY fecha DESC")->fetchAll();
        if (count($noticias) > 0):
            foreach ($noticias as $item):
        ?>
            <a href="blog.php?id=<?php echo (int)$item['id']; ?>" class="blog-list-item">
                <div class="blog-list-image">
                    <img src="<?php echo htmlspecialchars($item['imagen']); ?>"
                         alt="<?php echo htmlspecialchars($item['titulo']); ?>">
                </div>
                <div class="blog-list-content">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                        <span style="background:rgba(0,168,232,0.1); color:#00A8E8; padding:6px 15px; border-radius:20px; font-weight:600; font-size:0.85rem;">
                            <?php echo htmlspecialchars($item['tipo']); ?>
                        </span>
                        <span style="color:var(--text-gray); font-size:0.9rem;">
                            <i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($item['fecha'])); ?>
                        </span>
                    </div>
                    <h2 style="margin:0 0 15px; font-size:1.5rem; color:var(--klyp-dark); line-height:1.3;">
                        <?php echo htmlspecialchars($item['titulo']); ?>
                    </h2>
                    <p style="color:var(--text-gray); font-size:1rem; line-height:1.6; margin-bottom:25px; flex:1;">
                        <?php echo htmlspecialchars(mb_strimwidth($item['texto'], 0, 180, '...')); ?>
                    </p>
                    <div>
                        <span class="blog-list-btn-visual">
                            Leer artículo <i class="fas fa-arrow-right" style="margin-left:8px;"></i>
                        </span>
                    </div>
                </div>
            </a>
        <?php
            endforeach;
        else:
        ?>
            <div style="text-align:center; padding:80px 20px; background:white; border-radius:20px; box-shadow:0 10px 30px rgba(0,0,0,0.05);">
                <i class="fas fa-newspaper" style="font-size:4rem; color:#ddd; margin-bottom:20px;"></i>
                <h3 style="color:var(--klyp-dark); font-size:1.8rem; margin-bottom:10px;">Aún no hay publicaciones</h3>
                <p style="color:var(--text-gray); font-size:1.1rem;">Pronto compartiremos nuestras últimas novedades.</p>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php require 'includes/footer_front.php'; ?>

<script>
    document.getElementById('mobile-menu').addEventListener('click', function() {
        document.getElementById('nav-list').classList.toggle('active');
    });
</script>
</body>
</html>
