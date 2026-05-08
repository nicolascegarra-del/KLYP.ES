<?php
require 'header.php';
require '../db.php';

if (!isset($_GET['id'])) {
    header('Location: usuarios.php');
    exit;
}

$id = (int) $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $nombre  = $_POST['nombre'];
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $pdo->prepare('UPDATE usuarios_admin SET nombre = ?, usuario = ?, password = ? WHERE id = ?')
            ->execute([$nombre, $usuario, password_hash($password, PASSWORD_DEFAULT), $id]);
    } else {
        $pdo->prepare('UPDATE usuarios_admin SET nombre = ?, usuario = ? WHERE id = ?')
            ->execute([$nombre, $usuario, $id]);
    }

    header('Location: usuarios.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM usuarios_admin WHERE id = ?');
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

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <form method="POST">
        <?php echo csrf_field(); ?>
        <div class="form-group">
            <label>Nombre Completo</label>
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($user['nombre']); ?>" required>
        </div>
        <div class="form-group">
            <label>Usuario (Login)</label>
            <input type="text" name="usuario" value="<?php echo htmlspecialchars($user['usuario']); ?>" required autocomplete="off">
        </div>
        <div class="form-group">
            <label>Contraseña</label>
            <input type="password" name="password" placeholder="Dejar en blanco para mantener la actual" autocomplete="new-password">
            <small style="color:#888; display:block; margin-top:5px;">
                Solo escribe aquí si quieres cambiar la contraseña.
            </small>
        </div>
        <div style="margin-top:20px; display:flex; gap:10px;">
            <button type="submit" class="btn" style="flex:1;">Guardar Cambios</button>
            <a href="usuarios.php" class="btn" style="background-color:#8898aa; text-align:center; width:100px;">Cancelar</a>
        </div>
    </form>
</div>

</div></body></html>
