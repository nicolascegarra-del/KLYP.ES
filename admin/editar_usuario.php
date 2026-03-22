<?php
require 'header.php';
require '../db.php';

if (!isset($_GET['id'])) {
    header('Location: usuarios.php');
    exit;
}

$id = (int)$_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $nombre   = trim($_POST['nombre'] ?? '');
    $usuario  = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    $errores  = [];

    if (strlen($nombre) < 2)  $errores[] = 'El nombre debe tener al menos 2 caracteres.';
    if (strlen($usuario) < 3) $errores[] = 'El usuario debe tener al menos 3 caracteres.';
    if ($password !== '' && strlen($password) < 6) $errores[] = 'La contraseña debe tener al menos 6 caracteres.';

    // Comprobar usuario duplicado (excluyendo el propio)
    $check = $pdo->prepare("SELECT id FROM usuarios_admin WHERE usuario = ? AND id != ?");
    $check->execute([$usuario, $id]);
    if ($check->fetch()) $errores[] = 'Ese nombre de usuario ya está en uso.';

    if (empty($errores)) {
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $pdo->prepare("UPDATE usuarios_admin SET nombre=?, usuario=?, password=? WHERE id=?")
                ->execute([$nombre, $usuario, $hash, $id]);
        } else {
            $pdo->prepare("UPDATE usuarios_admin SET nombre=?, usuario=? WHERE id=?")
                ->execute([$nombre, $usuario, $id]);
        }
        $_SESSION['flash'] = 'Administrador actualizado correctamente.';
        header('Location: usuarios.php');
        exit;
    }
}

$stmt = $pdo->prepare("SELECT * FROM usuarios_admin WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: usuarios.php');
    exit;
}
?>

<div class="header">
    <h1 class="page-title">Editar Administrador</h1>
</div>

<?php if (!empty($errores)): ?>
    <div style="background:#f8d7da; color:#721c24; padding:15px; border-radius:8px; margin-bottom:20px;">
        <?php foreach ($errores as $e): ?><div>• <?php echo htmlspecialchars($e); ?></div><?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="card" style="max-width:600px; margin:0 auto;">
    <form method="POST">
        <?php echo csrf_field(); ?>
        <div class="form-group">
            <label>Nombre Completo</label>
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($user['nombre']); ?>" required minlength="2">
        </div>
        <div class="form-group">
            <label>Usuario (Login)</label>
            <input type="text" name="usuario" value="<?php echo htmlspecialchars($user['usuario']); ?>" required minlength="3">
        </div>
        <div class="form-group">
            <label>Nueva Contraseña</label>
            <input type="password" name="password" placeholder="Dejar en blanco para mantener la actual" minlength="6">
            <small style="color:#888; display:block; margin-top:5px;">
                Solo escribe aquí si quieres cambiar la contraseña. Mínimo 6 caracteres.
            </small>
        </div>
        <div style="margin-top:20px; display:flex; gap:10px;">
            <button type="submit" class="btn" style="flex:1;">Guardar Cambios</button>
            <a href="usuarios.php" class="btn" style="background:#8898aa; text-align:center; width:100px;">Cancelar</a>
        </div>
    </form>
</div>

</div></body></html>
