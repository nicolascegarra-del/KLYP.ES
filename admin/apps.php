<?php
require 'header.php';
require '../db.php';
require 'funciones.php';

// Borrar app
if (isset($_GET['borrar'])) {
    csrf_verify();
    $pdo->prepare("DELETE FROM aplicaciones WHERE id=?")->execute([(int)$_GET['borrar']]);
    $_SESSION['flash'] = 'Aplicación eliminada.';
    header('Location: apps.php');
    exit;
}

$errores = [];

// Crear app
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $titulo    = trim($_POST['titulo'] ?? '');
    $desc      = trim($_POST['descripcion'] ?? '');
    $contenido = trim($_POST['contenido_extendido'] ?? '');
    $icono     = trim($_POST['icono'] ?? '');
    $enlace    = trim($_POST['enlace'] ?? '#');

    if (strlen($titulo) < 2)  $errores[] = 'El título es obligatorio.';
    if (strlen($desc) < 5)    $errores[] = 'La descripción debe tener al menos 5 caracteres.';

    if (empty($errores)) {
        $pdo->prepare("INSERT INTO aplicaciones (titulo, descripcion, contenido_extendido, icono, enlace) VALUES (?,?,?,?,?)")
            ->execute([$titulo, $desc, $contenido, $icono, $enlace]);
        $_SESSION['flash'] = 'Aplicación añadida correctamente.';
        header('Location: apps.php');
        exit;
    }
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$iconos = getIcons();
?>

<div class="header">
    <h1 class="page-title">Gestión de Aplicaciones</h1>
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
        <h3><i class="fas fa-plus-circle"></i> Nueva App</h3>
        <form method="POST">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label>Título</label>
                <input type="text" name="titulo" placeholder="Ej: Klyp Nóminas" required
                       value="<?php echo htmlspecialchars($_POST['titulo'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Descripción Corta (Tarjeta)</label>
                <textarea name="descripcion" rows="2" required><?php echo htmlspecialchars($_POST['descripcion'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label>Información Detallada (+ info)</label>
                <textarea name="contenido_extendido" rows="5" placeholder="Explica aquí de qué trata la app..."><?php echo htmlspecialchars($_POST['contenido_extendido'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label>Enlace Externo (Opcional)</label>
                <input type="text" name="enlace" value="<?php echo htmlspecialchars($_POST['enlace'] ?? '#'); ?>">
            </div>
            <div class="form-group">
                <label>Icono</label>
                <select name="icono">
                    <?php foreach ($iconos as $cat => $lista): ?>
                        <optgroup label="<?php echo htmlspecialchars($cat); ?>">
                            <?php foreach ($lista as $k => $v): ?>
                                <option value="<?php echo htmlspecialchars($k); ?>"><?php echo htmlspecialchars($v); ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn" style="width:100%">Añadir App</button>
        </form>
    </div>

    <div class="card">
        <h3>Apps Activas</h3>
        <table>
            <thead><tr><th>Icono</th><th>App</th><th>Acciones</th></tr></thead>
            <tbody>
                <?php
                $apps = $pdo->query("SELECT * FROM aplicaciones");
                while ($row = $apps->fetch()):
                ?>
                <tr>
                    <td class="icon-preview"><i class="fas <?php echo htmlspecialchars($row['icono']); ?>"></i></td>
                    <td>
                        <strong><?php echo htmlspecialchars($row['titulo']); ?></strong><br>
                        <small><?php echo htmlspecialchars($row['descripcion']); ?></small>
                    </td>
                    <td>
                        <a href="editar.php?id=<?php echo (int)$row['id']; ?>"
                           style="color:var(--klyp-blue); margin-right:10px;"><i class="fas fa-edit"></i></a>
                        <a href="apps.php?borrar=<?php echo (int)$row['id']; ?>&_token=<?php echo csrf_token(); ?>"
                           onclick="return confirm('¿Borrar esta aplicación?')"
                           style="color:red;"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</div></body></html>
