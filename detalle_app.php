<?php
require 'db.php';

// Obtener la configuración para las redes sociales del footer
$config = $pdo->query("SELECT * FROM configuracion WHERE id = 1")->fetch();

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: index.php"); exit; }

$stmt = $pdo->prepare("SELECT * FROM aplicaciones WHERE id = ?");
$stmt->execute([$id]);
$app = $stmt->fetch();

if (!$app) { header("Location: index.php"); exit; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($app['titulo']); ?> | KLYP</title>
    <link rel="stylesheet" href="style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        /* =========================================
           ESTILOS VISTA DETALLE APP
           ========================================= */
        .app-post-content {
            max-width: 900px;
            margin: -80px auto 80px; /* Margen negativo para solaparse con el fondo azul */
            background: white;
            padding: 60px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            position: relative;
            z-index: 10;
            text-align: center;
        }
        
        .app-icon-large {
            font-size: 5rem;
            color: #00A8E8;
            margin-bottom: 20px;
        }

        .app-content-text {
            text-align: left;
            color: #444;
            line-height: 1.8;
            font-size: 1.1rem;
            white-space: pre-wrap; /* Mantiene los saltos de línea */
            margin-top: 40px;
            margin-bottom: 50px;
            border-top: 1px solid #eee;
            padding-top: 40px;
        }

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
            .app-post-content { padding: 40px 20px; margin-top: -40px; }
            .hero h1 { font-size: 2.2rem !important; line-height: 1.2; padding: 0 10px; }
            
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
                padding: 10px 0 !important;
                margin: 0 !important;
                box-shadow: 0 10px 20px rgba(0,0,0,0.5);
                z-index: 9999;
            }
            
            #nav-list.active { display: flex !important; }
            #nav-list li { margin: 0 !important; padding: 0 !important; text-align: center; }
            #nav-list li a { font-size: 1.05rem !important; display: block !important; padding: 10px 20px !important; line-height: 1 !important; }
            #nav-list li a.btn-client { display: inline-block !important; margin-top: 5px !important; margin-bottom: 5px !important; padding: 8px 20px !important; }
        }
    </style>
</head>
<body style="background-color: #f4f7fa;">

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
                    <li><a href="index.php#nosotros">¿Quiénes somos?</a></li>
                    <li><a href="index.php#que-hacemos">¿Qué hacemos?</a></li>
                    <li><a href="blog.php">Blog</a></li>
                    <li><a href="index.php#contacto">Contacto</a></li>
                    <li><a href="#" class="btn-client">Acceso Cliente</a></li>
                </ul>
            </nav>
        </header>

        <section class="hero" style="padding: 60px 20px; text-align: center;">
            <div style="max-width: 800px; margin: 0 auto;">
                <h1 style="font-size: 2.5rem; line-height: 1.3; margin-bottom: 15px;">App <span class="highlight"><?php echo htmlspecialchars($app['titulo']); ?></span></h1>
            </div>
        </section>
    </div>

    <div class="app-post-content">
        <div class="app-icon-large">
            <i class="fas <?php echo htmlspecialchars($app['icono']); ?>"></i>
        </div>
        
        <h2 style="color: #001133; font-size: 2rem; margin-bottom: 15px;"><?php echo htmlspecialchars($app['titulo']); ?></h2>
        <p style="color: #888; font-size: 1.1rem; max-width: 600px; margin: 0 auto;"><?php echo htmlspecialchars($app['descripcion']); ?></p>
        
        <div class="app-content-text">
            <?php 
            echo !empty($app['contenido_extendido']) ? htmlspecialchars($app['contenido_extendido']) : "Próximamente más información detallada sobre esta aplicación..."; 
            ?>
        </div>

        <div style="text-align: center;">
            <a href="index.php" class="btn" style="background: transparent; border: 2px solid #00A8E8; color: #00A8E8; padding: 12px 30px; border-radius: 50px; font-weight: 600; text-decoration: none;">
                <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Volver al inicio
            </a>
        </div>
    </div>

    <footer>
        <div class="container" style="text-align: center;">
            <p>&copy; <?php echo date('Y'); ?> KLYP. Todos los derechos reservados.</p>
            
            <div class="social-links" style="margin: 15px 0;">
                <?php if (!empty($config['twitter'])): ?><a href="<?php echo htmlspecialchars($config['twitter']); ?>" target="_blank"><i class="fab fa-twitter"></i></a><?php endif; ?>
                <?php if (!empty($config['instagram'])): ?><a href="<?php echo htmlspecialchars($config['instagram']); ?>" target="_blank"><i class="fab fa-instagram"></i></a><?php endif; ?>
                <?php if (!empty($config['linkedin'])): ?><a href="<?php echo htmlspecialchars($config['linkedin']); ?>" target="_blank"><i class="fab fa-linkedin"></i></a><?php endif; ?>
                <?php if (!empty($config['facebook'])): ?><a href="<?php echo htmlspecialchars($config['facebook']); ?>" target="_blank"><i class="fab fa-facebook"></i></a><?php endif; ?>
            </div>

            <div style="margin-top: 15px; font-size: 0.85rem; display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
                <a href="legal.php?id=aviso" style="color: rgba(255,255,255,0.7); text-decoration: none;">Aviso Legal</a>
                <a href="legal.php?id=privacidad" style="color: rgba(255,255,255,0.7); text-decoration: none;">Política de Privacidad</a>
                <a href="legal.php?id=cookies" style="color: rgba(255,255,255,0.7); text-decoration: none;">Política de Cookies</a>
            </div>

            <div style="margin-top: 25px;">
                <a href="admin/index.php" style="color: rgba(255,255,255,0.4); text-decoration: none; font-size: 0.75rem; transition: color 0.3s;">
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