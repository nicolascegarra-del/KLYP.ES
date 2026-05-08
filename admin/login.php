<?php
require_once 'security.php';
session_secure_start();

if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

if (file_exists('../db.php')) {
    require '../db.php';
} else {
    die('Error de configuración del servidor.');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario  = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');

    try {
        $stmt = $pdo->prepare('SELECT * FROM usuarios_admin WHERE usuario = :u LIMIT 1');
        $stmt->execute([':u' => $usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $autenticado = false;

        if ($user) {
            $hash = $user['password'];
            if (str_starts_with($hash, '$2y$') || str_starts_with($hash, '$2b$')) {
                $autenticado = password_verify($password, $hash);
            } else {
                $autenticado = ($password === $hash);
                if ($autenticado) {
                    $pdo->prepare('UPDATE usuarios_admin SET password = ? WHERE id = ?')
                        ->execute([password_hash($password, PASSWORD_DEFAULT), $user['id']]);
                }
            }
        }

        if ($autenticado) {
            session_regenerate_id(true);
            $_SESSION['usuario_id']     = $user['id'];
            $_SESSION['usuario_nombre'] = $user['nombre'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos.';
        }
    } catch (Exception $e) {
        $error = 'Error interno. Inténtalo de nuevo.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klyp | Acceso al panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Nunito', sans-serif;
            background: #051937;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255,255,255,0.05) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        body::after {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 70% 30%, rgba(46,109,180,0.2) 0%, transparent 70%),
                radial-gradient(ellipse 40% 40% at 20% 80%, rgba(26,58,107,0.3) 0%, transparent 60%);
            pointer-events: none;
        }

        .login-wrap {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 44px 40px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.35);
        }

        .login-logo {
            display: flex;
            justify-content: center;
            margin-bottom: 32px;
        }

        .login-logo img { height: 36px; filter: invert(1) brightness(0) saturate(0) invert(20%) sepia(80%) saturate(600%) hue-rotate(195deg) brightness(50%); }

        .login-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #051937;
            text-align: center;
            margin-bottom: 6px;
        }

        .login-sub {
            font-size: 0.88rem;
            color: #6B7280;
            text-align: center;
            margin-bottom: 32px;
        }

        .form-group { margin-bottom: 16px; }

        .form-group label {
            display: block;
            font-size: 0.82rem;
            font-weight: 700;
            color: #051937;
            margin-bottom: 6px;
        }

        .input-wrap { position: relative; }

        .input-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
            font-size: 0.9rem;
        }

        .input-wrap input {
            width: 100%;
            padding: 12px 14px 12px 40px;
            border: 1.5px solid #E5EAF4;
            border-radius: 8px;
            font-family: 'Nunito', sans-serif;
            font-size: 0.95rem;
            color: #1F2937;
            background: #fafbfd;
            outline: none;
            transition: all 0.2s ease;
        }

        .input-wrap input:focus {
            border-color: #2E6DB4;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(46,109,180,0.15);
        }

        .error-msg {
            background: #FEE2E2;
            color: #DC2626;
            border: 1px solid #fca5a5;
            border-radius: 8px;
            padding: 11px 14px;
            font-size: 0.88rem;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: #051937;
            color: #fff;
            border: none;
            border-radius: 50px;
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 6px 20px rgba(5,25,55,0.25);
        }

        .btn-login:hover {
            background: #2E6DB4;
            box-shadow: 0 8px 24px rgba(46,109,180,0.35);
            transform: translateY(-1px);
        }

        .login-back {
            display: block;
            text-align: center;
            margin-top: 24px;
            font-size: 0.85rem;
            color: rgba(255,255,255,0.45);
            transition: color 0.2s;
        }

        .login-back:hover { color: rgba(255,255,255,0.8); }
    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="login-card">
            <div class="login-logo">
                <img src="../assets/img/logo_white.png" alt="Klyp">
            </div>
            <h1 class="login-title">Panel de administración</h1>
            <p class="login-sub">Introduce tus credenciales para acceder</p>

            <?php if ($error): ?>
                <div class="error-msg">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Usuario</label>
                    <div class="input-wrap">
                        <i class="fas fa-user"></i>
                        <input type="text" name="usuario" placeholder="Tu usuario" required autocomplete="username">
                    </div>
                </div>
                <div class="form-group">
                    <label>Contraseña</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="••••••••" required autocomplete="current-password">
                    </div>
                </div>
                <button type="submit" class="btn-login">Entrar al panel</button>
            </form>
        </div>
        <a href="../index.php" class="login-back">
            <i class="fas fa-arrow-left" style="margin-right:6px;"></i> Volver a la web
        </a>
    </div>
</body>
</html>
