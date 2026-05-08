<?php require 'header.php'; require '../db.php'; require 'funciones.php';

// Borrar app — solo por POST con CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrar'])) {
    csrf_verify();
    $pdo->prepare('DELETE FROM aplicaciones WHERE id = ?')->execute([(int)$_POST['borrar']]);
    header('Location: apps.php');
    exit;
}

// Crear app
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titulo'])) {
    csrf_verify();
    $pdo->prepare('INSERT INTO aplicaciones (titulo, descripcion, contenido_extendido, icono, enlace) VALUES (?, ?, ?, ?, ?)')
        ->execute([
            $_POST['titulo'],
            $_POST['descripcion'],
            $_POST['contenido_extendido'],
            $_POST['icono'],
            $_POST['enlace'],
        ]);
    header('Location: apps.php');
    exit;
}

$iconos = getIcons();
?>

<div class="header">
    <h1 class="page-title">Gestión de Aplicaciones</h1>
</div>

<div style="display:grid; grid-template-columns:1fr 2fr; gap:20px;">
    <div class="card">
        <h3><i class="fas fa-plus-circle"></i> Nueva App</h3>
        <form method="POST">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label>Título</label>
                <input type="text" name="titulo" placeholder="Ej: Klyp Nóminas" required>
            </div>
            <div class="form-group">
                <label>Descripción Corta (Tarjeta)</label>
                <textarea name="descripcion" rows="2" required></textarea>
            </div>
            <div class="form-group">
                <label>Información Detallada (+ info)</label>
                <textarea name="contenido_extendido" rows="5" placeholder="Explica aquí de qué trata la app..."></textarea>
            </div>
            <div class="form-group">
                <label>Enlace Externo (Opcional)</label>
                <input type="text" name="enlace" value="#">
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
                $apps = $pdo->query('SELECT * FROM aplicaciones');
                while ($row = $apps->fetch()):
                ?>
                <tr>
                    <td class="icon-preview"><i class="fas <?php echo htmlspecialchars($row['icono']); ?>"></i></td>
                    <td>
                        <strong><?php echo htmlspecialchars($row['titulo']); ?></strong>
                        <br><small><?php echo htmlspecialchars($row['descripcion']); ?></small>
                    </td>
                    <td>
                        <a href="editar.php?id=<?php echo $row['id']; ?>"
                           style="color:var(--klyp-blue); margin-right:10px;"><i class="fas fa-edit"></i></a>
                        <form method="POST" style="display:inline;"
                              onsubmit="return confirm('¿Borrar esta app?')">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="borrar" value="<?php echo $row['id']; ?>">
                            <button type="submit" style="background:none; border:none; color:red; cursor:pointer; padding:0;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</div></body></html>
