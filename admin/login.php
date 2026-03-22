<?php
session_start();
require_once __DIR__ . '/csrf.php';

// Cabeceras de seguridad
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');

// Si ya está logueado, redirigir
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require '../db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verificar token CSRF
    csrf_verify();

    // Rate limiting: máx 5 intentos en 5 minutos por IP de sesión
    if (!rate_limit_check('login', 5, 300)) {
        $error = 'Demasiados intentos fallidos. Espera unos minutos antes de volver a intentarlo.';
    } else {
        $usuario  = trim($_POST['usuario'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($usuario === '' || $password === '') {
            $error = 'Rellena todos los campos.';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT * FROM usuarios_admin WHERE usuario = :u LIMIT 1");
                $stmt->execute([':u' => $usuario]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['password'])) {
                    rate_limit_reset('login');
                    session_regenerate_id(true);
                    $_SESSION['usuario_id']     = $user['id'];
                    $_SESSION['usuario_nombre'] = $user['nombre'];
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Usuario o contraseña incorrectos.';
                }
            } catch (Exception $e) {
                $error = 'Error de conexión. Inténtalo de nuevo.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KLYP | Acceso Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body { margin:0; font-family:'Poppins',sans-serif; background:#001133; height:100vh; display:flex; align-items:center; justify-content:center; }
        .login-card { background:white; width:90%; max-width:400px; padding:40px; border-radius:15px; text-align:center; box-shadow:0 10px 30px rgba(0,0,0,0.3); }
        h2 { color:#001133; margin-top:0; }
        input { width:100%; padding:12px; margin-bottom:15px; border:1px solid #ddd; border-radius:8px; box-sizing:border-box; font-family:inherit; }
        button { width:100%; padding:12px; background:#00A8E8; color:white; border:none; border-radius:25px; font-weight:bold; cursor:pointer; font-family:inherit; font-size:1rem; }
        button:hover { background:#008ec4; }
        .error { color:red; margin-bottom:15px; font-size:0.9rem; background:#fff0f0; padding:10px; border-radius:6px; }
        .back { display:block; margin-top:20px; color:#666; text-decoration:none; font-size:0.9rem; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Panel Klyp</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <?php echo csrf_field(); ?>
            <input type="text" name="usuario" placeholder="Usuario" required autocomplete="off">
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Entrar</button>
        </form>
        <a href="../index.php" class="back">← Volver a la web</a>
    </div>
</body>
</html>
