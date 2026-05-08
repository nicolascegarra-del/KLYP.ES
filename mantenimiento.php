<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KLYP | Próximamente</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Nunito', sans-serif;
            background: #0f1c2e;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px 20px 80px;
            overflow-x: hidden;
        }

        .bg-glow {
            position: fixed;
            top: -200px;
            left: 50%;
            transform: translateX(-50%);
            width: min(800px, 100vw);
            height: min(800px, 100vw);
            background: radial-gradient(circle, rgba(46,109,180,0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        .logo {
            margin-bottom: 48px;
        }
        .logo img {
            height: 48px;
            max-width: 180px;
            width: auto;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(46,109,180,0.2);
            border: 1px solid rgba(46,109,180,0.4);
            color: #60a5fa;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 6px 16px;
            border-radius: 999px;
            margin-bottom: 32px;
        }

        h1 {
            font-size: clamp(1.75rem, 5vw, 3.5rem);
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 20px;
            color: #fff;
        }
        h1 span {
            color: #60a5fa;
        }

        p {
            font-size: clamp(0.95rem, 2.5vw, 1.1rem);
            color: rgba(255,255,255,0.6);
            max-width: 480px;
            line-height: 1.7;
            margin-bottom: 48px;
            padding: 0 4px;
        }

        .divider {
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #2E6DB4, #60a5fa);
            border-radius: 999px;
            margin: 0 auto 48px;
        }

        .features {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 56px;
            width: 100%;
            max-width: 720px;
        }
        .feature {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 16px 24px;
            font-size: 0.95rem;
            color: rgba(255,255,255,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            flex: 1 1 160px;
        }
        .feature-icon {
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        footer {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.25);
            margin-top: auto;
            padding-top: 32px;
        }

        @media (max-width: 480px) {
            body {
                padding: 32px 16px 48px;
                justify-content: flex-start;
                padding-top: 48px;
            }
            .logo {
                margin-bottom: 32px;
            }
            .logo img {
                height: 38px;
                max-width: 140px;
            }
            .badge {
                font-size: 0.78rem;
                margin-bottom: 24px;
            }
            h1 {
                font-size: clamp(1.5rem, 7vw, 2.2rem);
                margin-bottom: 16px;
            }
            p {
                margin-bottom: 32px;
            }
            .divider {
                margin-bottom: 32px;
            }
            .features {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
                margin-bottom: 40px;
            }
            .feature {
                flex: none;
                width: 100%;
                padding: 14px 20px;
            }
            footer {
                font-size: 0.72rem;
                padding-top: 24px;
            }
        }

        @media (min-width: 481px) and (max-width: 768px) {
            body {
                padding: 40px 24px 60px;
            }
            .features {
                gap: 14px;
            }
            .feature {
                flex: 1 1 40%;
            }
        }
    </style>
</head>
<body>

<div class="bg-glow"></div>

<div class="logo">
    <img src="assets/img/logo_white.png" alt="Klyp">
</div>

<div class="badge">🚧 En construcción</div>

<h1>Estamos preparando<br>algo <span>increíble</span></h1>

<p>Nuestra web está en desarrollo. Pronto estará lista con todas las novedades de Klyp. ¡Volvemos enseguida!</p>

<div class="divider"></div>

<div class="features">
    <div class="feature">
        <span class="feature-icon">💻</span> Software a medida
    </div>
    <div class="feature">
        <span class="feature-icon">🤖</span> Inteligencia Artificial
    </div>
    <div class="feature">
        <span class="feature-icon">☁️</span> Cloud & DevOps
    </div>
</div>

<footer>&copy; <?php echo date('Y'); ?> KLYP · Desarrollo de Software e IA &nbsp;·&nbsp; <a href="admin/index.php" style="color:rgba(255,255,255,0.3); text-decoration:none;">Panel Admin</a></footer>

</body>
</html>
