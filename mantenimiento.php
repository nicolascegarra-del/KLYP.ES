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
            padding: 40px 20px;
            overflow: hidden;
        }

        .bg-glow {
            position: fixed;
            top: -200px;
            left: 50%;
            transform: translateX(-50%);
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(46,109,180,0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        .logo {
            margin-bottom: 48px;
        }
        .logo img {
            height: 48px;
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
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 20px;
            color: #fff;
        }
        h1 span {
            color: #60a5fa;
        }

        p {
            font-size: 1.1rem;
            color: rgba(255,255,255,0.6);
            max-width: 480px;
            line-height: 1.7;
            margin-bottom: 48px;
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
            gap: 24px;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 56px;
        }
        .feature {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 20px 28px;
            font-size: 0.95rem;
            color: rgba(255,255,255,0.7);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .feature-icon {
            font-size: 1.4rem;
        }

        footer {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.25);
            position: fixed;
            bottom: 24px;
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

<footer>&copy; <?php echo date('Y'); ?> KLYP · Desarrollo de Software e IA</footer>

</body>
</html>
