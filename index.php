<?php
session_start();
require 'db.php';

// Cabeceras de seguridad
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

$config = $pdo->query("SELECT * FROM configuracion WHERE id = 1")->fetch();

// CSRF token para el formulario de contacto (token de un solo uso)
if (empty($_SESSION['csrf_contact'])) {
    $_SESSION['csrf_contact'] = bin2hex(random_bytes(32));
}

$contacto_ok    = $_SESSION['contacto_ok']    ?? null;
$contacto_error = $_SESSION['contacto_error'] ?? null;
unset($_SESSION['contacto_ok'], $_SESSION['contacto_error']);

// SEO
$meta_desc = htmlspecialchars(mb_strimwidth(
    strip_tags($config['texto_nosotros'] ?? 'KLYP - Soluciones inteligentes para la gestión de nóminas y contabilidad.'),
    0, 155, '...'
));
$og_url = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'klyp.es') . '/';

function render_escaparate_blog($pdo, $config) {
    $limite = isset($config['num_noticias_landing']) ? (int)$config['num_noticias_landing'] : 3;
    if ($limite <= 0) return;
    $noticias = $pdo->query("SELECT * FROM blog ORDER BY fecha DESC LIMIT $limite")->fetchAll();
    if (count($noticias) === 0) return;
    ?>
    <section id="novedades-landing" style="padding:80px 20px; background-color:#f4f7fa;">
        <div class="container">
            <div class="section-title">
                <h2>Últimas <span class="highlight">Novedades</span></h2>
                <p class="text-content" style="text-align:center;">Descubre las actualizaciones de nuestras App's y noticias corporativas.</p>
            </div>
            <div style="display:flex; flex-wrap:wrap; justify-content:center; gap:30px; max-width:1200px; margin:50px auto 0;">
                <?php foreach ($noticias as $noticia): ?>
                <div class="card" style="padding:0; overflow:hidden; display:flex; flex-direction:column; text-align:left; transition:transform 0.3s; background:white; box-shadow:0 10px 20px rgba(0,0,0,0.05); flex:0 1 350px; width:100%; max-width:350px;">
                    <div style="width:100%; height:200px; background:#fafbfc; display:flex; align-items:center; justify-content:center; padding:15px; border-bottom:1px solid #f0f0f0;">
                        <img src="<?php echo htmlspecialchars($noticia['imagen']); ?>"
                             alt="<?php echo htmlspecialchars($noticia['titulo']); ?>"
                             style="max-width:100%; max-height:100%; object-fit:contain;">
                    </div>
                    <div style="padding:25px; flex:1; display:flex; flex-direction:column;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; font-size:0.85rem;">
                            <span style="background:rgba(0,168,232,0.1); color:#00A8E8; padding:5px 12px; border-radius:20px; font-weight:600;">
                                <?php echo htmlspecialchars($noticia['tipo']); ?>
                            </span>
                            <span style="color:var(--text-gray);">
                                <i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($noticia['fecha'])); ?>
                            </span>
                        </div>
                        <h3 style="margin:0 0 10px; font-size:1.2rem; color:var(--klyp-dark);"><?php echo htmlspecialchars($noticia['titulo']); ?></h3>
                        <p style="color:var(--text-gray); font-size:0.95rem; margin-bottom:25px; flex:1;">
                            <?php echo htmlspecialchars(mb_strimwidth($noticia['texto'], 0, 120, '...')); ?>
                        </p>
                        <a href="blog.php?id=<?php echo $noticia['id']; ?>" class="btn-cta"
                           style="text-align:center; padding:12px; font-size:0.95rem; background-color:#00A8E8 !important; color:white !important;">
                           Leer más
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php
}

$posicion_blog = $config['posicion_noticias'] ?? 'sobre_contacto';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KLYP | Gestión Inteligente</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo $meta_desc; ?>">
    <meta name="robots" content="index, follow">

    <!-- Open Graph -->
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="<?php echo htmlspecialchars($og_url); ?>">
    <meta property="og:title"       content="KLYP | Gestión Inteligente">
    <meta property="og:description" content="<?php echo $meta_desc; ?>">
    <meta property="og:image"       content="<?php echo htmlspecialchars($og_url); ?>assets/img/logo_white.png">

    <!-- Twitter Card -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="KLYP | Gestión Inteligente">
    <meta name="twitter:description" content="<?php echo $meta_desc; ?>">

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="canonical" href="<?php echo htmlspecialchars($og_url); ?>">

    <!-- Schema.org Organization -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "KLYP",
        "url": "<?php echo htmlspecialchars($og_url); ?>",
        "logo": "<?php echo htmlspecialchars($og_url); ?>assets/img/logo_white.png",
        "contactPoint": {
            "@type": "ContactPoint",
            "contactType": "customer service",
            "email": "<?php echo htmlspecialchars($config['email_contacto'] ?? ''); ?>"
        }
    }
    </script>

    <style>
        .hero-container { display:flex; align-items:center; justify-content:space-between; gap:40px; }
        .hero-text { flex:1; max-width:500px; }
        .hero-image { flex:1; display:flex; justify-content:center; align-items:center; position:relative; }
        .hero-image svg { max-width:100%; height:auto; animation:float 8s ease-in-out infinite; opacity:0.8; }
        @keyframes float { 0%{transform:translateY(0)} 50%{transform:translateY(-12px)} 100%{transform:translateY(0)} }
        #apps-directas { margin-top:-140px; position:relative; z-index:10; background:transparent; }
        .menu-toggle { display:none; color:white; font-size:2rem; cursor:pointer; padding:5px; }
        @media(max-width:768px){
            .hero-image,.hero-image svg{display:none !important;}
            .hero-container{flex-direction:column;text-align:center;gap:20px;}
            .hero-text{max-width:100% !important;margin:0 auto;padding:0 15px;}
            .hero-text h1{font-size:2.2rem !important;line-height:1.2;}
            header{position:relative;display:flex !important;justify-content:space-between !important;align-items:center !important;flex-wrap:wrap;padding:15px 20px !important;}
            .menu-toggle{display:block !important;}
            nav{width:100%;}
            #nav-list{display:none !important;flex-direction:column;width:100%;background-color:#000c24 !important;position:absolute;top:100%;left:0;padding:10px 0 !important;margin:0 !important;box-shadow:0 10px 20px rgba(0,0,0,0.5);z-index:9999;}
            #nav-list.active{display:flex !important;}
            #nav-list li{margin:0 !important;padding:0 !important;text-align:center;}
            #nav-list li a{font-size:1.05rem !important;display:block !important;padding:10px 20px !important;line-height:1 !important;}
            #nav-list li a.btn-client{display:inline-block !important;margin-top:5px !important;margin-bottom:5px !important;padding:8px 20px !important;}
        }
    </style>
</head>
<body>

<div class="hero-wrapper" style="padding-bottom:150px;">
    <header style="padding:20px; display:flex; justify-content:space-between; align-items:center; position:relative;">
        <a href="index.php" style="display:flex; align-items:center; text-decoration:none;">
            <img src="assets/img/logo_white.png" alt="KLYP - Soluciones de gestión empresarial" style="height:45px; cursor:pointer;">
        </a>
        <div class="menu-toggle" id="mobile-menu"><i class="fas fa-bars"></i></div>
        <nav>
            <ul id="nav-list">
                <li><a href="#nosotros">¿Quiénes somos?</a></li>
                <li><a href="#que-hacemos">¿Qué hacemos?</a></li>
                <li><a href="blog.php">Blog</a></li>
                <li><a href="#contacto">Contacto</a></li>
                <li><a href="#" class="btn-client">Acceso Cliente</a></li>
            </ul>
        </nav>
    </header>

    <section class="hero" style="padding:40px 0;">
        <div class="hero-container">
            <div class="hero-text">
                <h1>Impulsa tu negocio con <span class="highlight">KLYP</span></h1>
                <span class="subtitle">Soluciones inteligentes para la gestión de nóminas y contabilidad.</span>
                <br><br>
                <a href="#apps-directas" class="btn-cta">Descubrir Apps</a>
            </div>
            <div class="hero-image">
                <svg width="500" height="350" viewBox="0 0 500 350" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M250 175 L125 90" stroke="#00A8E8" stroke-opacity="0.3" stroke-width="1.5"/>
                    <path d="M250 175 L375 90" stroke="#00A8E8" stroke-opacity="0.3" stroke-width="1.5"/>
                    <path d="M250 175 L125 260" stroke="#00A8E8" stroke-opacity="0.3" stroke-width="1.5"/>
                    <path d="M250 175 L375 260" stroke="#00A8E8" stroke-opacity="0.3" stroke-width="1.5"/>
                    <path d="M250 175 L420 175" stroke="#00A8E8" stroke-opacity="0.3" stroke-width="1.5"/>
                    <path d="M125 90 L75 140" stroke="#00A8E8" stroke-opacity="0.2" stroke-width="1.5"/>
                    <rect x="100" y="70" width="50" height="50" rx="12" fill="rgba(255,255,255,0.03)" stroke="#00A8E8" stroke-opacity="0.4" stroke-width="1.5"/>
                    <rect x="350" y="70" width="50" height="50" rx="12" fill="rgba(255,255,255,0.03)" stroke="#00A8E8" stroke-opacity="0.4" stroke-width="1.5"/>
                    <rect x="100" y="235" width="50" height="50" rx="12" fill="rgba(255,255,255,0.03)" stroke="#00A8E8" stroke-opacity="0.4" stroke-width="1.5"/>
                    <rect x="350" y="235" width="50" height="50" rx="12" fill="rgba(255,255,255,0.03)" stroke="#00A8E8" stroke-opacity="0.4" stroke-width="1.5"/>
                    <rect x="400" y="150" width="50" height="50" rx="12" fill="rgba(255,255,255,0.03)" stroke="#00A8E8" stroke-opacity="0.4" stroke-width="1.5"/>
                    <g filter="url(#glow_soft_lg)">
                        <rect x="215" y="140" width="70" height="70" rx="18" fill="#00A8E8" fill-opacity="0.6"/>
                        <rect x="225" y="150" width="25" height="25" rx="8" fill="#ffffff" fill-opacity="0.5"/>
                        <circle cx="265" cy="190" r="5" fill="#ffffff" fill-opacity="0.8"/>
                    </g>
                    <defs>
                        <filter id="glow_soft_lg" x="190" y="120" width="120" height="120" filterUnits="userSpaceOnUse">
                            <feGaussianBlur stdDeviation="12" result="blur"/>
                            <feComposite in="SourceGraphic" in2="blur" operator="over"/>
                        </filter>
                    </defs>
                </svg>
            </div>
        </div>
    </section>
</div>

<?php if ($posicion_blog == 'sobre_apps') render_escaparate_blog($pdo, $config); ?>

<section id="apps-directas" style="padding-bottom:60px;">
    <div class="cards-container">
        <?php
        $stmt = $pdo->query("SELECT * FROM aplicaciones");
        while ($app = $stmt->fetch()):
        ?>
        <div class="card">
            <div class="icon-circle">
                <i class="fas <?php echo htmlspecialchars($app['icono']); ?>"></i>
            </div>
            <h3><?php echo htmlspecialchars($app['titulo']); ?></h3>
            <p><?php echo htmlspecialchars($app['descripcion']); ?></p>
            <a href="detalle_app.php?id=<?php echo $app['id']; ?>" class="btn-card">+ info</a>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<?php if ($posicion_blog == 'sobre_nosotros') render_escaparate_blog($pdo, $config); ?>

<section id="nosotros" style="padding:80px 20px; background-color:var(--white);">
    <div class="container">
        <div class="section-title">
            <h2>¿Quiénes <span class="highlight">somos</span>?</h2>
        </div>
        <p class="text-content" style="text-align:center; max-width:800px; margin:0 auto; line-height:1.8;">
            <?php echo nl2br(htmlspecialchars($config['texto_nosotros'] ?? '')); ?>
        </p>
    </div>
</section>

<?php if ($posicion_blog == 'sobre_hacemos') render_escaparate_blog($pdo, $config); ?>

<section id="que-hacemos" style="padding:80px 20px; background-color:#f9f9f9;">
    <div class="container">
        <div class="section-title">
            <h2>¿Qué <span class="highlight">hacemos</span>?</h2>
        </div>
        <p class="text-content" style="text-align:center; max-width:800px; margin:0 auto; line-height:1.8;">
            <?php echo nl2br(htmlspecialchars($config['texto_hacemos'] ?? '')); ?>
        </p>
    </div>
</section>

<?php if ($posicion_blog == 'sobre_contacto') render_escaparate_blog($pdo, $config); ?>

<section id="contacto" style="padding:80px 20px 120px;">
    <div class="section-title">
        <h2>Contacto</h2>
        <p class="text-content" style="text-align:center;">¿Hablamos? Estamos a un clic de distancia.</p>
    </div>

    <?php if ($contacto_ok): ?>
        <div style="max-width:600px; margin:0 auto 20px; background:#d4edda; color:#155724; padding:15px; border-radius:8px; text-align:center;">
            <?php echo htmlspecialchars($contacto_ok); ?>
        </div>
    <?php endif; ?>
    <?php if ($contacto_error): ?>
        <div style="max-width:600px; margin:0 auto 20px; background:#f8d7da; color:#721c24; padding:15px; border-radius:8px; text-align:center;">
            <?php echo htmlspecialchars($contacto_error); ?>
        </div>
    <?php endif; ?>

    <div class="contact-form">
        <form action="procesar_contacto.php" method="POST">
            <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_contact']; ?>">
            <input type="text"  name="nombre"  placeholder="Tu Nombre"  required minlength="2">
            <input type="email" name="email"   placeholder="Tu Email"   required>
            <textarea name="mensaje" placeholder="Cuéntanos qué necesitas..." rows="5" required minlength="10"></textarea>
            <button type="submit" class="btn-submit">Enviar Mensaje</button>
        </form>
    </div>
</section>

<?php if ($posicion_blog == 'sobre_footer') render_escaparate_blog($pdo, $config); ?>

<?php require 'includes/footer_front.php'; ?>

<script>
    document.getElementById('mobile-menu').addEventListener('click', function() {
        document.getElementById('nav-list').classList.toggle('active');
    });
</script>
</body>
</html>
