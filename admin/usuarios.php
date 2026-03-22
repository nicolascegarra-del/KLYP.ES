<?php
require 'header.php';
require '../db.php';

// Borrar usuario (no puede borrarse a sí mismo)
if (isset($_GET['borrar'])) {
    csrf_verify();
    $id_borrar = (int)$_GET['borrar'];
    if ($id_borrar !== (int)$_SESSION['usuario_id']) {
        $pdo->prepare("DELETE FROM usuarios_admin WHERE id = ?")->execute([$id_borrar]);
    }
    $_SESSION['flash'] = 'Administrador eliminado.';
    header('Location: usuarios.php');
    exit;
}

// Crear usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $nombre   = trim($_POST['nombre'] ?? '');
    $usuario  = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    $errores  = [];

    if (strlen($nombre) < 2)    $errores[] = 'El nombre debe tener al menos 2 caracteres.';
    if (strlen($usuario) < 3)   $errores[] = 'El usuario debe tener al menos 3 caracteres.';
    if (strlen($password) < 6)  $errores[] = 'La contraseña debe tener al menos 6 caracteres.';

    // Comprobar usuario duplicado
    $check = $pdo->prepare("SELECT id FROM usuarios_admin WHERE usuario = ?");
    $check->execute([$usuario]);
    if ($check->fetch()) $errores[] = 'Ese nombre de usuario ya existe.';

    if (empty($errores)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $pdo->prepare("INSERT INTO usuarios_admin (nombre, usuario, password) VALUES (?, ?, ?)")
            ->execute([$nombre, $usuario, $hash]);
        $_SESSION['flash'] = 'Administrador creado correctamente.';
        header('Location: usuarios.php');
        exit;
    }
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>

<div class="header">
    <h1 class="page-title">Gestión de Administradores</h1>
</div>

<?php if ($flash): ?>
    <div style="background:#d4edda; color:#155724; padding:15px; border-radius:8px; margin-bottom:20px;"><?php echo htmlspecialchars($flash); ?></div>
<?php endif; ?>

<?php if (!empty($errores)): ?>
    <div style="background:#f8d7da; color:#721c24; padding:15px; border-radius:8px; margin-bottom:20px;">
        <?php foreach ($errores as $e): ?><div>• <?php echo htmlspecialchars($e); ?></div><?php endforeach; ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">

    <div class="card">
        <h3>Crear Nuevo Admin</h3>
        <form method="POST">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label>Nombre Completo</label>
                <input type="text" name="nombre" required minlength="2" value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Usuario (Login)</label>
                <input type="text" name="usuario" required minlength="3" value="<?php echo htmlspecialchars($_POST['usuario'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" required minlength="6">
                <small style="color:#888">Mínimo 6 caracteres.</small>
            </div>
            <button type="submit" class="btn" style="width:100%">Crear Admin</button>
        </form>
    </div>

    <div class="card">
        <h3>Administradores Actuales</h3>
        <table>
            <thead><tr><th>Nombre</th><th>Usuario</th><th>Acciones</th></tr></thead>
            <tbody>
                <?php
                $users = $pdo->query("SELECT * FROM usuarios_admin");
                while ($u = $users->fetch()):
                    $esYo = ($u['id'] == $_SESSION['usuario_id']);
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($u['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($u['usuario']); ?></td>
                    <td>
                        <a href="editar_usuario.php?id=<?php echo (int)$u['id']; ?>"
                           class="btn" style="background-color:#f39c12; padding:5px 10px; font-size:0.8rem; margin-right:5px;"
                           title="Editar"><i class="fas fa-edit"></i></a>
                        <?php if (!$esYo): ?>
                            <a href="usuarios.php?borrar=<?php echo (int)$u['id']; ?>&_token=<?php echo csrf_token(); ?>"
                               class="btn-danger"
                               onclick="return confirm('¿Seguro que quieres eliminar este administrador?')"
                               title="Eliminar"><i class="fas fa-trash"></i></a>
                        <?php else: ?>
                            <span style="color:#ccc; font-size:0.8rem; margin-left:5px;">(Tú)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</div></body></html>
