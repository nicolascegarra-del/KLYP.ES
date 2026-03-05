<?php 
require 'db.php'; 
// Obtener la configuración
$config = $pdo->query("SELECT * FROM configuracion WHERE id = 1")->fetch();

function render_escaparate_blog($pdo, $config) {
    $limite = isset($config['num_noticias_landing']) ? (int)$config['num_noticias_landing'] : 3;
    if ($limite <= 0) return;

    $noticias = $pdo->query("SELECT * FROM blog ORDER BY fecha DESC LIMIT $limite")->fetchAll();
    
    if(count($noticias) === 0) return; 
    ?>
    <section id="novedades-landing" style="padding: 80px 20px; background-color: #f4f7fa;">
        <div class="container">
            <div class="section-title">
                <h2>Últimas <span class="highlight">Novedades</span></h2>
                <p class="text-content" style="text-align: center;">Descubre las actualizaciones de nuestras App's y noticias corporativas.</p>
            </div>
            
            <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 30px; max-width: 1200px; margin: 50px auto 0;">
                <?php foreach ($noticias as $noticia): ?>
                    <div class="card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column; text-align: left; transition: transform 0.3s; background: white; box-shadow: 0 10px 20px rgba(0,0,0,0.05); flex: 0 1 350px; width: 100%; max-width: 350px;">
                        
                        <div style="width: 100%; height: 200px; background-color: #fafbfc; display: flex; align-items: center; justify-content: center; padding: 15px; border-bottom: 1px solid #f0f0f0;">
                            <img src="<?php echo htmlspecialchars($noticia['imagen']); ?>" alt="Noticia" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        </div>

                        <div style="padding: 25px; flex: 1; display: flex; flex-direction: column;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; font-size: 0.85rem;">
                                <span style="background: rgba(0, 168, 232, 0.1); color: #00A8E8; padding: 5px 12px; border-radius: 20px; font-weight: 600;">
                                    <?php echo htmlspecialchars($noticia['tipo']); ?>
                                </span>
                                <span style="color: var(--text-gray);">
                                    <i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($noticia['fecha'])); ?>
                                </span>
                            </div>
                            <h3 style="margin: 0 0 10px 0; font-size: 1.2rem; color: var(--klyp-dark);"><?php echo htmlspecialchars($noticia['titulo']); ?></h3>
                            <p style="color: var(--text-gray); font-size: 0.95rem; margin-bottom: 25px; flex: 1;">
                                <?php echo htmlspecialchars(mb_strimwidth($noticia['texto'], 0, 120, "...")); ?>
                            </p>
                            <a href="blog.php?id=<?php echo $noticia['id']; ?>" class="btn-cta" style="text-align: center; padding: 12px; font-size: 0.95rem; background-color: #00A8E8 !important; color: white !important;">Leer más</a>
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
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        .hero-container { display: flex; align-items: center; justify-content: space-between; gap: 40px; }
        .hero-text { flex: 1; max-width: 500px; }
        .hero-image { flex: 1; display: flex; justify-content: center; align-items: center; position: relative; }
        .hero-image svg { max-width: 100%; height: auto; animation: float 8s ease-in-out infinite; opacity: 0.8; }
        @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-12px); } 100% { transform: translateY(0px); } }
        #apps-directas { margin-top: -140px; position: relative; z-index: 10; background: transparent; }

        /* =========================================
           ADAPTACIÓN MÓVIL BLINDADA (RESPONSIVE)
           ========================================= */
        .menu-toggle {
            display: none;
            color: white;
            font-size: 2rem;
            cursor: pointer;
            padding: 5px;
        }

        @media (max-width: 768px) {
            .hero-image, .hero-image svg { display: none !important; }
            .hero-container { flex-direction: column; text-align: center; gap: 20px; }
            .hero-text { max-width: 100% !important; margin: 0 auto; padding: 0 15px; }
            .hero-text h1 { font-size: 2.2rem !important; line-height: 1.2; }
            
            header { 
                position: relative; 
                display: flex !important; 
                justify-content: space-between !important; 
                align-items: center !important; 
                flex-wrap: wrap; 
                padding: 15px 20px !important; 
            }
            .menu-toggle { display: block !important; }
            nav { width: 100%; }
            
            #nav-list {
                display: none !important; 
                flex-direction: column;
                width: 100%;
                background-color: #000c24 !important; 
                position: absolute;
                top: 100%; 
                left: 0;
                padding: 10px 0 !important; /* Mínimo relleno exterior */
                margin: 0 !important;
                box-shadow: 0 10px 20px rgba(0,0,0,0.5);
                z-index: 9999;
            }
            
            #nav-list.active {
                display: flex !important; 
            }
            
            /* Ajuste súper compacto para los elementos del menú */
            #nav-list li { 
                margin: 0 !important; 
                padding: 0 !important; 
                text-align: center; 
            }
            
            #nav-list li a { 
                font-size: 1.05rem !important; 
                display: block !important; 
                padding: 10px 20px !important; /* Área clicable compacta */
                line-height: 1 !important;
            }

            /* Ajuste específico para el botón de Acceso Cliente */
            #nav-list li a.btn-client {
                display: inline-block !important;
                margin-top: 5px !important;
                margin-bottom: 5px !important;
                padding: 8px 20px !important;
            }
        }
    </style>
</head>
<body>

    <div class="hero-wrapper" style="padding-bottom: 150px;"> 
        <header style="padding: 20px; display: flex; justify-content: space-between; align-items: center; position: relative;">
            <a href="index.php" style="display: flex; align-items: center; text-decoration: none;">
                <img src="assets/img/logo_white.png" alt="Klyp Logo" style="height: 45px; cursor: pointer;">
            </a>
            
            <div class="menu-toggle" id="mobile-menu">
                <i class="fas fa-bars"></i>
            </div>

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

        <section class="hero" style="padding: 40px 0;"> 
            <div class="hero-container">
                <div class="hero-text">
                    <h1>Impulsa tu negocio con <span class="highlight">KLYP</span></h1>
                    <span class="subtitle">Soluciones inteligentes para la gestión de nóminas y contabilidad.</span>
                    <br><br>
                    <a href="#apps-directas" class="btn-cta">Descubrir Apps</a>
                </div>
                
                <div class="hero-image">
                    <svg width="500" height="350" viewBox="0 0 500 350" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                        <rect x="50" y="120" width="40" height="40" rx="10" fill="rgba(255,255,255,0.03)" stroke="#00A8E8" stroke-opacity="0.2" stroke-width="1.5"/>
                        <g filter="url(#glow_soft_lg)">
                            <rect x="215" y="140" width="70" height="70" rx="18" fill="#00A8E8" fill-opacity="0.6"/>
                            <rect x="225" y="150" width="25" height="25" rx="8" fill="#ffffff" fill-opacity="0.5"/>
                            <circle cx="265" cy="190" r="5" fill="#ffffff" fill-opacity="0.8"/>
                        </g>
                        <circle cx="180" cy="120" r="4" fill="#00A8E8" fill-opacity="0.5"/>
                        <circle cx="300" cy="230" r="3" fill="#ffffff" fill-opacity="0.3"/>
                        <circle cx="350" cy="160" r="5" fill="#00A8E8" fill-opacity="0.4"/>
                        <circle cx="150" cy="210" r="3" fill="#ffffff" fill-opacity="0.2"/>
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

    <?php if($posicion_blog == 'sobre_apps') render_escaparate_blog($pdo, $config); ?>

    <section id="apps-directas" style="padding-bottom: 60px;">
        <div class="cards-container">
            <?php
            $stmt = $pdo->query("SELECT * FROM aplicaciones");
            while ($app = $stmt->fetch()) {
            ?>
            <div class="card">
                <div class="icon-circle">
                    <i class="fas <?php echo htmlspecialchars($app['icono']); ?>"></i>
                </div>
                <h3><?php echo htmlspecialchars($app['titulo']); ?></h3>
                <p><?php echo htmlspecialchars($app['descripcion']); ?></p>
                <a href="detalle_app.php?id=<?php echo $app['id']; ?>" class="btn-card">+ info</a>
            </div>
            <?php } ?>
        </div>
    </section>

    <?php if($posicion_blog == 'sobre_nosotros') render_escaparate_blog($pdo, $config); ?>

    <section id="nosotros" style="padding: 80px 20px; background-color: var(--white);">
        <div class="container">
            <div class="section-title">
                <h2>¿Quiénes <span class="highlight">somos</span>?</h2>
            </div>
            <p class="text-content" style="text-align: center; max-width: 800px; margin: 0 auto; line-height: 1.8;">
                <?php echo nl2br(htmlspecialchars($config['texto_nosotros'] ?? '')); ?>
            </p>
        </div>
    </section>

    <?php if($posicion_blog == 'sobre_hacemos') render_escaparate_blog($pdo, $config); ?>

    <section id="que-hacemos" style="padding: 80px 20px; background-color: #f9f9f9;">
        <div class="container">
            <div class="section-title">
                <h2>¿Qué <span class="highlight">hacemos</span>?</h2>
            </div>
            <p class="text-content" style="text-align: center; max-width: 800px; margin: 0 auto; line-height: 1.8;">
                <?php echo nl2br(htmlspecialchars($config['texto_hacemos'] ?? '')); ?>
            </p>
        </div>
    </section>

    <?php if($posicion_blog == 'sobre_contacto') render_escaparate_blog($pdo, $config); ?>

    <section id="contacto" style="padding: 80px 20px 120px;">
        <div class="section-title">
            <h2>Contacto</h2>
            <p class="text-content" style="text-align: center;">¿Hablamos? Estamos a un clic de distancia.</p>
        </div>
        
        <div class="contact-form">
            <form action="#">
                <input type="text" placeholder="Tu Nombre" required>
                <input type="email" placeholder="Tu Email" required>
                <textarea placeholder="Cuéntanos qué necesitas..." rows="5" required></textarea>
                <button type="submit" class="btn-submit">Enviar Mensaje</button>
            </form>
        </div>
    </section>

    <?php if($posicion_blog == 'sobre_footer') render_escaparate_blog($pdo, $config); ?>

  <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> KLYP. Todos los derechos reservados.</p>
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
            <div style="margin-top: 25px;">
                <a href="admin/index.php" style="color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.85rem; transition: color 0.3s;">
                    <i class="fas fa-cog" style="margin-right: 5px;"></i> Panel de Administrador
                </a>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('mobile-menu').addEventListener('click', function() {
            document.getElementById('nav-list').classList.toggle('active');
        });
    </script>

</body>
</html>