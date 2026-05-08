<?php
require 'db.php';
$config = $pdo->query('SELECT * FROM configuracion WHERE id = 1')->fetch();
if (!empty($config['modo_desarrollo'])) { include 'mantenimiento.php'; exit; }
$posicion_blog = $config['posicion_noticias'] ?? 'sobre_contacto';

function render_blog_section($pdo, $config) {
    $limite = max(1, (int)($config['num_noticias_landing'] ?? 3));
    $noticias = $pdo->query("SELECT * FROM blog ORDER BY fecha DESC LIMIT $limite")->fetchAll();
    if (empty($noticias)) return;
    ?>
    <section class="blog-section">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Novedades</span>
                <h2>Últimas <span class="accent">noticias</span></h2>
                <p>Descubre las actualizaciones de nuestras apps y noticias del ecosistema Klyp.</p>
            </div>
            <div class="blog-grid">
                <?php foreach ($noticias as $n): ?>
                <a href="blog.php?id=<?php echo $n['id']; ?>" class="blog-card">
                    <?php if ($n['imagen']): ?>
                        <img class="blog-card-img" src="<?php echo htmlspecialchars($n['imagen']); ?>" alt="">
                    <?php else: ?>
                        <div class="blog-card-img-placeholder"><i class="fas fa-newspaper"></i></div>
                    <?php endif; ?>
                    <div class="blog-card-body">
                        <div class="blog-card-meta">
                            <span class="blog-card-tag"><?php echo htmlspecialchars($n['tipo']); ?></span>
                            <span class="blog-card-date">
                                <i class="far fa-calendar-alt"></i>
                                <?php echo date('d/m/Y', strtotime($n['fecha'])); ?>
                            </span>
                        </div>
                        <h3><?php echo htmlspecialchars($n['titulo']); ?></h3>
                        <p><?php echo htmlspecialchars(mb_strimwidth($n['texto'], 0, 110, '…')); ?></p>
                        <span class="blog-card-more">Leer más <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <div class="blog-section-footer">
                <a href="blog.php" class="btn-outline">Ver todas las noticias <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </section>
    <?php
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KLYP | Desarrollo de Software e Inteligencia Artificial</title>
    <meta name="description" content="Klyp — Soluciones de software, inteligencia artificial y transformación digital para empresas.">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

<!-- ====== NAVBAR ====== -->
<nav class="navbar" id="navbar">
    <a href="index.php" class="navbar-logo">
        <img src="assets/img/logo_white.png" alt="Klyp">
    </a>
    <button class="nav-toggle" id="nav-toggle" aria-label="Menú">
        <span></span><span></span><span></span>
    </button>
    <ul class="nav-links" id="nav-links">
        <li><a href="#nosotros">Quiénes somos</a></li>
        <li><a href="#que-hacemos">Qué hacemos</a></li>
        <li><a href="blog.php">Blog</a></li>
        <li><a href="#contacto">Contacto</a></li>
        <li><a href="#" class="btn-client">Acceso Cliente</a></li>
    </ul>
</nav>

<!-- ====== HERO ====== -->
<section class="hero">
    <div class="hero-inner">
        <div class="hero-text">
            <div class="hero-badge">
                <i class="fas fa-microchip"></i> Software · IA · Transformación Digital
            </div>
            <h1 class="hero-title">
                Tecnología que impulsa tu <span class="accent">negocio</span>
            </h1>
            <p class="hero-subtitle">
                Desarrollamos aplicaciones de gestión, implementamos inteligencia artificial y acompañamos a las empresas en su transformación digital.
            </p>
            <div class="hero-actions">
                <a href="#apps-directas" class="btn-primary">
                    <i class="fas fa-th-large"></i> Ver nuestras apps
                </a>
                <a href="#contacto" class="btn-ghost">
                    Hablar con nosotros <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="hero-graphic">
            <svg viewBox="0 0 520 420" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Connections -->
                <line x1="260" y1="210" x2="100" y2="100" stroke="#2E6DB4" stroke-opacity="0.25" stroke-width="1.5"/>
                <line x1="260" y1="210" x2="420" y2="100" stroke="#2E6DB4" stroke-opacity="0.25" stroke-width="1.5"/>
                <line x1="260" y1="210" x2="100" y2="320" stroke="#2E6DB4" stroke-opacity="0.25" stroke-width="1.5"/>
                <line x1="260" y1="210" x2="420" y2="320" stroke="#2E6DB4" stroke-opacity="0.25" stroke-width="1.5"/>
                <line x1="260" y1="210" x2="260" y2="60"  stroke="#2E6DB4" stroke-opacity="0.25" stroke-width="1.5"/>
                <line x1="100" y1="100" x2="60"  y2="180" stroke="#2E6DB4" stroke-opacity="0.12" stroke-width="1"/>
                <line x1="420" y1="100" x2="480" y2="180" stroke="#2E6DB4" stroke-opacity="0.12" stroke-width="1"/>
                <line x1="100" y1="320" x2="60"  y2="280" stroke="#2E6DB4" stroke-opacity="0.12" stroke-width="1"/>

                <!-- Satellite nodes -->
                <rect x="72"  y="72"  width="56" height="56" rx="14" fill="rgba(46,109,180,0.08)" stroke="#2E6DB4" stroke-opacity="0.35" stroke-width="1.5"/>
                <text x="100" y="107" text-anchor="middle" font-family="sans-serif" font-size="20" fill="#4A8FD4" opacity="0.7">⚙️</text>

                <rect x="392" y="72"  width="56" height="56" rx="14" fill="rgba(46,109,180,0.08)" stroke="#2E6DB4" stroke-opacity="0.35" stroke-width="1.5"/>
                <text x="420" y="107" text-anchor="middle" font-family="sans-serif" font-size="18" fill="#4A8FD4" opacity="0.7">🤖</text>

                <rect x="72"  y="292" width="56" height="56" rx="14" fill="rgba(46,109,180,0.08)" stroke="#2E6DB4" stroke-opacity="0.35" stroke-width="1.5"/>
                <text x="100" y="327" text-anchor="middle" font-family="sans-serif" font-size="18" fill="#4A8FD4" opacity="0.7">📊</text>

                <rect x="392" y="292" width="56" height="56" rx="14" fill="rgba(46,109,180,0.08)" stroke="#2E6DB4" stroke-opacity="0.35" stroke-width="1.5"/>
                <text x="420" y="327" text-anchor="middle" font-family="sans-serif" font-size="18" fill="#4A8FD4" opacity="0.7">☁️</text>

                <rect x="232" y="32"  width="56" height="56" rx="14" fill="rgba(46,109,180,0.08)" stroke="#2E6DB4" stroke-opacity="0.35" stroke-width="1.5"/>
                <text x="260" y="67"  text-anchor="middle" font-family="sans-serif" font-size="18" fill="#4A8FD4" opacity="0.7">💡</text>

                <!-- Central node glow -->
                <circle cx="260" cy="210" r="48" fill="rgba(46,109,180,0.08)" stroke="#2E6DB4" stroke-opacity="0.2" stroke-width="1"/>
                <circle cx="260" cy="210" r="36" fill="rgba(46,109,180,0.15)" stroke="#2E6DB4" stroke-opacity="0.4" stroke-width="1.5"/>
                <circle cx="260" cy="210" r="20" fill="#2E6DB4" fill-opacity="0.8"/>
                <text x="260" y="218" text-anchor="middle" font-family="sans-serif" font-size="16" fill="white">K</text>

                <!-- Floating dots -->
                <circle cx="180" cy="150" r="3" fill="#60a5fa" fill-opacity="0.5"/>
                <circle cx="340" cy="270" r="3" fill="#60a5fa" fill-opacity="0.4"/>
                <circle cx="160" cy="270" r="2" fill="#60a5fa" fill-opacity="0.3"/>
                <circle cx="360" cy="150" r="2" fill="#60a5fa" fill-opacity="0.4"/>
                <circle cx="480" cy="200" r="4" fill="#2E6DB4" fill-opacity="0.3"/>
                <circle cx="40"  cy="240" r="3" fill="#2E6DB4" fill-opacity="0.25"/>
            </svg>
        </div>
    </div>
    <a href="#apps-directas" class="hero-scroll">Descubrir</a>
</section>

<!-- ====== FEATURES STRIP ====== -->
<div class="features-strip">
    <div class="container">
        <div class="feature-item">
            <div class="feature-icon"><i class="fas fa-code"></i></div>
            <div>
                <h4>Desarrollo a medida</h4>
                <p>Aplicaciones diseñadas específicamente para los procesos y necesidades de tu empresa.</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon"><i class="fas fa-brain"></i></div>
            <div>
                <h4>Inteligencia Artificial</h4>
                <p>Implementamos IA en tus flujos de trabajo para automatizar, analizar y predecir.</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon"><i class="fas fa-headset"></i></div>
            <div>
                <h4>Soporte continuo</h4>
                <p>Equipo técnico dedicado, mantenimiento proactivo y evolución constante de tus soluciones.</p>
            </div>
        </div>
    </div>
</div>

<?php if ($posicion_blog === 'sobre_apps') render_blog_section($pdo, $config); ?>

<!-- ====== APPS ====== -->
<section class="apps-section" id="apps-directas">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Nuestras apps</span>
            <h2>Soluciones <span class="accent">que ya funcionan</span></h2>
            <p>Un ecosistema de aplicaciones listo para usar, pensado para la gestión empresarial moderna.</p>
        </div>
        <div class="apps-grid">
            <?php
            $apps = $pdo->query('SELECT * FROM aplicaciones')->fetchAll();
            foreach ($apps as $app):
            ?>
            <a href="detalle_app.php?id=<?php echo $app['id']; ?>" class="app-card">
                <div class="app-card-icon">
                    <i class="fas <?php echo htmlspecialchars($app['icono']); ?>"></i>
                </div>
                <h3><?php echo htmlspecialchars($app['titulo']); ?></h3>
                <p><?php echo htmlspecialchars($app['descripcion']); ?></p>
                <span class="app-card-link">Ver más <i class="fas fa-arrow-right"></i></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if ($posicion_blog === 'sobre_nosotros') render_blog_section($pdo, $config); ?>

<!-- ====== ABOUT ====== -->
<section class="about-section" id="nosotros">
    <div class="container">
        <div class="about-grid">
            <div class="about-text">
                <span class="section-tag">Quiénes somos</span>
                <h2>Tecnología con <span class="accent">propósito</span></h2>
                <p><?php echo nl2br(htmlspecialchars($config['texto_nosotros'] ?? '')); ?></p>
            </div>
            <div class="about-visual">
                <div class="about-card-stack">
                    <div class="stat-card">
                        <div class="stat-card-icon"><i class="fas fa-rocket"></i></div>
                        <div>
                            <div class="stat-card-value">100%</div>
                            <div class="stat-card-label">Proyectos entregados a tiempo</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-icon"><i class="fas fa-users"></i></div>
                        <div>
                            <div class="stat-card-value">+50</div>
                            <div class="stat-card-label">Clientes satisfechos</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-icon"><i class="fas fa-shield-alt"></i></div>
                        <div>
                            <div class="stat-card-value">24/7</div>
                            <div class="stat-card-label">Soporte y monitorización</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if ($posicion_blog === 'sobre_hacemos') render_blog_section($pdo, $config); ?>

<!-- ====== SERVICES ====== -->
<section class="services-section" id="que-hacemos">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Qué hacemos</span>
            <h2 style="color:var(--white);">
                Servicios que <span style="color:#60a5fa;">marcan la diferencia</span>
            </h2>
            <p style="color:rgba(255,255,255,0.6);">
                <?php echo nl2br(htmlspecialchars($config['texto_hacemos'] ?? '')); ?>
            </p>
        </div>
        <div class="services-grid">
            <div class="service-card">
                <div class="service-card-icon"><i class="fas fa-laptop-code"></i></div>
                <h4>Desarrollo de software</h4>
                <p>Aplicaciones web y de escritorio adaptadas a cada sector, con tecnología moderna y escalable.</p>
            </div>
            <div class="service-card">
                <div class="service-card-icon"><i class="fas fa-robot"></i></div>
                <h4>Implementación de IA</h4>
                <p>Integración de modelos de lenguaje, automatización inteligente y análisis predictivo en tu operativa.</p>
            </div>
            <div class="service-card">
                <div class="service-card-icon"><i class="fas fa-database"></i></div>
                <h4>Gestión de datos</h4>
                <p>Arquitecturas de datos robustas, migración, limpieza y visualización para tomar decisiones fundamentadas.</p>
            </div>
            <div class="service-card">
                <div class="service-card-icon"><i class="fas fa-cloud"></i></div>
                <h4>Cloud & DevOps</h4>
                <p>Despliegue, escalado y mantenimiento de infraestructura en la nube con alta disponibilidad.</p>
            </div>
        </div>
    </div>
</section>

<?php if ($posicion_blog === 'sobre_contacto') render_blog_section($pdo, $config); ?>

<!-- ====== CONTACT ====== -->
<section class="contact-section" id="contacto">
    <div class="container">
        <div class="contact-grid">
            <div class="contact-info">
                <span class="section-tag">Contacto</span>
                <h2>Hablemos de tu proyecto</h2>
                <p>¿Tienes una idea o un reto tecnológico? Cuéntanoslo. Nuestro equipo te responderá en menos de 24 horas.</p>
                <?php if (!empty($config['email_contacto'])): ?>
                <div class="contact-detail">
                    <div class="contact-detail-icon"><i class="fas fa-envelope"></i></div>
                    <?php echo htmlspecialchars($config['email_contacto']); ?>
                </div>
                <?php endif; ?>
                <div class="contact-detail">
                    <div class="contact-detail-icon"><i class="fas fa-map-marker-alt"></i></div>
                    España
                </div>
            </div>

            <div class="contact-form-card">
                <form action="mailto:<?php echo htmlspecialchars($config['email_contacto'] ?? ''); ?>" method="GET">
                    <div class="form-group">
                        <label>Tu nombre</label>
                        <input type="text" name="nombre" placeholder="Adrián García" required>
                    </div>
                    <div class="form-group">
                        <label>Tu email</label>
                        <input type="email" name="email" placeholder="hola@empresa.com" required>
                    </div>
                    <div class="form-group">
                        <label>¿En qué podemos ayudarte?</label>
                        <textarea name="mensaje" placeholder="Cuéntanos tu proyecto o necesidad..." required></textarea>
                    </div>
                    <button type="submit" class="btn-submit">Enviar mensaje <i class="fas fa-paper-plane" style="margin-left:8px;"></i></button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php if ($posicion_blog === 'sobre_footer') render_blog_section($pdo, $config); ?>

<!-- ====== FOOTER ====== -->
<footer>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <img src="assets/img/logo_white.png" alt="Klyp">
                <p>Desarrollo de software, inteligencia artificial y transformación digital para empresas que quieren crecer.</p>
            </div>
            <div class="footer-col">
                <h5>Navegación</h5>
                <ul>
                    <li><a href="#nosotros">Quiénes somos</a></li>
                    <li><a href="#que-hacemos">Qué hacemos</a></li>
                    <li><a href="blog.php">Blog</a></li>
                    <li><a href="#contacto">Contacto</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h5>Síguenos</h5>
                <div class="social-links">
                    <?php if (!empty($config['twitter'])): ?>
                        <a href="<?php echo htmlspecialchars($config['twitter']); ?>" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($config['instagram'])): ?>
                        <a href="<?php echo htmlspecialchars($config['instagram']); ?>" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($config['linkedin'])): ?>
                        <a href="<?php echo htmlspecialchars($config['linkedin']); ?>" target="_blank" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($config['facebook'])): ?>
                        <a href="<?php echo htmlspecialchars($config['facebook']); ?>" target="_blank" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
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
// Navbar scroll
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 40);
}, { passive: true });

// Mobile menu
const toggle = document.getElementById('nav-toggle');
const navLinks = document.getElementById('nav-links');
toggle.addEventListener('click', () => {
    navLinks.classList.toggle('open');
});

// Close menu on link click
navLinks.querySelectorAll('a').forEach(a => {
    a.addEventListener('click', () => navLinks.classList.remove('open'));
});
</script>

</body>
</html>
