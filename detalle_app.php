<?php
require 'db.php';

// Cabeceras de seguridad
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');

$config = $pdo->query("SELECT * FROM configuracion WHERE id = 1")->fetch();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM aplicaciones WHERE id = ?");
$stmt->execute([$id]);
$app = $stmt->fetch();
if (!$app) { header('Location: index.php'); exit; }

// SEO
$og_url    = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'klyp.es');
$meta_desc = htmlspecialchars(mb_strimwidth(
    strip_tags($app['descripcion'] ?? ''),
    0, 155, '...'
));
$canonical = $og_url . '/detalle_app.php?id=' . $id;
$mostrar_legales = true;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($app['titulo']); ?> | KLYP</title>

    <!-- SEO -->
    <meta name="description" content="<?php echo $meta_desc; ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical); ?>">

    <!-- Open Graph -->
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="<?php echo htmlspecialchars($canonical); ?>">
    <meta property="og:title"       content="<?php echo htmlspecialchars($app['titulo']); ?> | KLYP">
    <meta property="og:description" content="<?php echo $meta_desc; ?>">

    <!-- Twitter Card -->
    <meta name="twitter:card"        content="summary">
    <meta name="twitter:title"       content="<?php echo htmlspecialchars($app['titulo']); ?> | KLYP">
    <meta name="twitter:description" content="<?php echo $meta_desc; ?>">

    <!-- Schema.org SoftwareApplication -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "SoftwareApplication",
        "name": "<?php echo htmlspecialchars($app['titulo'], ENT_QUOTES); ?>",
        "description": "<?php echo htmlspecialchars($app['descripcion'] ?? '', ENT_QUOTES); ?>",
        "applicationCategory": "BusinessApplication",
        "offers": { "@type": "Offer", "price": "0", "priceCurrency": "EUR" }
    }
    </script>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        .app-post-content { max-width:900px; margin:-80px auto 80px; background:white; padding:60px; border-radius:20px; box-shadow:0 15px 35px rgba(0,0,0,0.1); position:relative; z-index:10; text-align:center; }
        .app-icon-large { font-size:5rem; color:#00A8E8; margin-bottom:20px; }
        .app-content-text { text-align:left; color:#444; line-height:1.8; font-size:1.1rem; white-space:pre-wrap; margin-top:40px; margin-bottom:50px; border-top:1px solid #eee; padding-top:40px; }
        .menu-toggle { display:none; color:white; font-size:2rem; cursor:pointer; padding:5px; }
        @media(max-width:768px){
            .app-post-content{padding:40px 20px;margin-top:-40px;}
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
                <li><a href="blog.php">Blog</a></li>
                <li><a href="index.php#contacto">Contacto</a></li>
                <li><a href="#" class="btn-client">Acceso Cliente</a></li>
            </ul>
        </nav>
    </header>

    <section class="hero" style="padding:60px 20px; text-align:center;">
        <div style="max-width:800px; margin:0 auto;">
            <h1 style="font-size:2.5rem; line-height:1.3; margin-bottom:15px;">
                App <span class="highlight"><?php echo htmlspecialchars($app['titulo']); ?></span>
            </h1>
        </div>
    </section>
</div>

<div class="app-post-content">
    <div class="app-icon-large">
        <i class="fas <?php echo htmlspecialchars($app['icono']); ?>" aria-hidden="true"></i>
    </div>
    <h2 style="color:#001133; font-size:2rem; margin-bottom:15px;"><?php echo htmlspecialchars($app['titulo']); ?></h2>
    <p style="color:#888; font-size:1.1rem; max-width:600px; margin:0 auto;"><?php echo htmlspecialchars($app['descripcion']); ?></p>
    <div class="app-content-text">
        <?php echo !empty($app['contenido_extendido'])
            ? htmlspecialchars($app['contenido_extendido'])
            : 'Próximamente más información detallada sobre esta aplicación.'; ?>
    </div>
    <div style="text-align:center;">
        <a href="index.php" class="btn" style="background:transparent; border:2px solid #00A8E8; color:#00A8E8; padding:12px 30px; border-radius:50px; font-weight:600; text-decoration:none;">
            <i class="fas fa-arrow-left" style="margin-right:8px;"></i> Volver al inicio
        </a>
    </div>
</div>

<?php require 'includes/footer_front.php'; ?>

<script>
    document.getElementById('mobile-menu').addEventListener('click', function() {
        document.getElementById('nav-list').classList.toggle('active');
    });
</script>
</body>
</html>
