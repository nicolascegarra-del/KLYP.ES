<?php require 'header.php'; require '../db.php';

// Borrar usuario — solo por POST con CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrar'])) {
    csrf_verify();
    $id_borrar = (int) $_POST['borrar'];
    if ($id_borrar !== (int) $_SESSION['usuario_id']) {
        $pdo->prepare('DELETE FROM usuarios_admin WHERE id = ?')->execute([$id_borrar]);
    }
    header('Location: usuarios.php');
    exit;
}

// Crear usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
    csrf_verify();
    $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $pdo->prepare('INSERT INTO usuarios_admin (nombre, usuario, password) VALUES (?, ?, ?)')
        ->execute([$_POST['nombre'], $_POST['usuario'], $hash]);
    header('Location: usuarios.php');
    exit;
}
?>

<div class="header">
    <h1 class="page-title">Gestión de Administradores</h1>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">

    <div class="card">
        <h3>Crear Nuevo Admin</h3>
        <form method="POST">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label>Nombre Completo</label>
                <input type="text" name="nombre" required>
            </div>
            <div class="form-group">
                <label>Usuario (Login)</label>
                <input type="text" name="usuario" required autocomplete="off">
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" required autocomplete="new-password">
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
                $users = $pdo->query('SELECT * FROM usuarios_admin');
                while ($u = $users->fetch()):
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($u['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($u['usuario']); ?></td>
                    <td>
                        <a href="editar_usuario.php?id=<?php echo $u['id']; ?>" class="btn"
                           style="background-color:#f39c12; padding:5px 10px; font-size:0.8rem; margin-right:5px;" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <?php if ($u['id'] != $_SESSION['usuario_id']): ?>
                        <form method="POST" style="display:inline;"
                              onsubmit="return confirm('¿Seguro que quieres eliminar a este administrador?')">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="borrar" value="<?php echo $u['id']; ?>">
                            <button type="submit" class="btn-danger" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
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
