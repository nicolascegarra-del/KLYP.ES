<?php
require 'header.php';
require '../db.php';

$MIMES_PERMITIDOS = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

// Borrar noticia — solo por POST con CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrar'])) {
    csrf_verify();
    $id_borrar = (int) $_POST['borrar'];
    $stmt = $pdo->prepare('SELECT imagen FROM blog WHERE id = ?');
    $stmt->execute([$id_borrar]);
    $noticia = $stmt->fetch();
    if ($noticia && $noticia['imagen'] && file_exists('../' . $noticia['imagen'])) {
        unlink('../' . $noticia['imagen']);
    }
    $pdo->prepare('DELETE FROM blog WHERE id = ?')->execute([$id_borrar]);
    header('Location: blog.php?msg=borrado');
    exit;
}

// Guardar/editar noticia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_noticia'])) {
    csrf_verify();

    $titulo = $_POST['titulo'];
    $texto  = $_POST['texto'];
    $fecha  = $_POST['fecha'];
    $tipo   = $_POST['tipo'];
    $id     = $_POST['id'] ?? '';

    $ruta_imagen = $_POST['imagen_actual'] ?? '';
    $error_imagen = '';

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $_FILES['imagen']['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $MIMES_PERMITIDOS)) {
            $error_imagen = 'Tipo de archivo no permitido. Usa JPG, PNG, GIF o WebP.';
        } else {
            $ext = match($mime) {
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/gif'  => 'gif',
                'image/webp' => 'webp',
            };
            $nombre_archivo = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $destino = '../assets/img/blog/' . $nombre_archivo;

            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
                if ($ruta_imagen && file_exists('../' . $ruta_imagen)) {
                    unlink('../' . $ruta_imagen);
                }
                $ruta_imagen = 'assets/img/blog/' . $nombre_archivo;
            }
        }
    }

    if (!$error_imagen) {
        if ($id === '') {
            $pdo->prepare('INSERT INTO blog (titulo, texto, imagen, fecha, tipo) VALUES (?, ?, ?, ?, ?)')
                ->execute([$titulo, $texto, $ruta_imagen, $fecha, $tipo]);
            header('Location: blog.php?msg=creado');
        } else {
            $pdo->prepare('UPDATE blog SET titulo=?, texto=?, imagen=?, fecha=?, tipo=? WHERE id=?')
                ->execute([$titulo, $texto, $ruta_imagen, $fecha, $tipo, (int)$id]);
            header('Location: blog.php?msg=editado');
        }
        exit;
    }
}

$apps = $pdo->query('SELECT id, titulo FROM aplicaciones')->fetchAll();

$mensaje = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'creado')  $mensaje = 'Noticia publicada con éxito.';
    if ($_GET['msg'] === 'editado') $mensaje = 'Noticia actualizada con éxito.';
    if ($_GET['msg'] === 'borrado') $mensaje = 'Noticia eliminada correctamente.';
}
?>

<div class="header" style="display:flex; justify-content:space-between; align-items:center;">
    <h1 class="page-title">Blog y Novedades</h1>
    <?php if (!isset($_GET['accion'])): ?>
        <a href="blog.php?accion=nuevo" class="btn" style="background:var(--klyp-blue);">+ Nueva Publicación</a>
    <?php endif; ?>
</div>

<?php if ($mensaje): ?>
    <div style="background:#d4edda; color:#155724; padding:15px; border-radius:8px; margin-bottom:20px;">
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<?php if (isset($error_imagen)): ?>
    <div style="background:#f8d7da; color:#721c24; padding:15px; border-radius:8px; margin-bottom:20px;">
        <?php echo htmlspecialchars($error_imagen); ?>
    </div>
<?php endif; ?>

<?php
if (isset($_GET['accion']) && in_array($_GET['accion'], ['nuevo', 'editar'])):
    $noticia_editar = ['id'=>'', 'titulo'=>'', 'texto'=>'', 'imagen'=>'', 'fecha'=>date('Y-m-d'), 'tipo'=>'Corporativa'];
    if ($_GET['accion'] === 'editar' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT * FROM blog WHERE id = ?');
        $stmt->execute([(int)$_GET['id']]);
        $noticia_editar = $stmt->fetch() ?: $noticia_editar;
    }
?>
    <div class="card">
        <h3 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:10px;">
            <?php echo ($_GET['accion'] === 'nuevo') ? 'Redactar Nueva Noticia' : 'Editar Noticia'; ?>
        </h3>
        <form method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="guardar_noticia" value="1">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($noticia_editar['id']); ?>">
            <input type="hidden" name="imagen_actual" value="<?php echo htmlspecialchars($noticia_editar['imagen']); ?>">

            <div style="display:grid; grid-template-columns:2fr 1fr; gap:20px;">
                <div class="form-group">
                    <label>Título de la noticia:</label>
                    <input type="text" name="titulo" value="<?php echo htmlspecialchars($noticia_editar['titulo']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Fecha de publicación:</label>
                    <input type="date" name="fecha" value="<?php echo htmlspecialchars($noticia_editar['fecha']); ?>" required>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="form-group">
                    <label>Categoría / Referencia:</label>
                    <select name="tipo" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-family:'Poppins',sans-serif;">
                        <option value="Corporativa" <?php echo ($noticia_editar['tipo']==='Corporativa') ? 'selected' : ''; ?>>🏢 Noticia Corporativa</option>
                        <optgroup label="Referente a una App:">
                            <?php foreach ($apps as $app): ?>
                            <option value="<?php echo htmlspecialchars($app['titulo']); ?>"
                                <?php echo ($noticia_editar['tipo']===$app['titulo']) ? 'selected' : ''; ?>>
                                📱 App: <?php echo htmlspecialchars($app['titulo']); ?>
                            </option>
                            <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>
                <div class="form-group">
                    <label>Imagen Destacada <small style="color:#888;">(JPG, PNG, GIF, WebP)</small>:</label>
                    <input type="file" name="imagen" accept="image/jpeg,image/png,image/gif,image/webp"
                           <?php echo ($_GET['accion']==='nuevo') ? 'required' : ''; ?> style="padding:5px;">
                    <?php if ($noticia_editar['imagen']): ?>
                        <small style="display:block; margin-top:5px; color:var(--text-gray);">
                            Imagen actual:
                            <img src="../<?php echo htmlspecialchars($noticia_editar['imagen']); ?>"
                                 style="height:30px; vertical-align:middle; border-radius:4px; margin-left:5px;">
                        </small>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label>Contenido de la noticia:</label>
                <textarea name="texto" rows="8" required
                    style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-family:'Poppins',sans-serif; resize:vertical;"
                ><?php echo htmlspecialchars($noticia_editar['texto']); ?></textarea>
            </div>

            <div style="margin-top:20px; display:flex; gap:10px; justify-content:flex-end;">
                <a href="blog.php" class="btn" style="background:#e74c3c;">Cancelar</a>
                <button type="submit" class="btn" style="background:#2ecc71;">Guardar Noticia</button>
            </div>
        </form>
    </div>

<?php else:
    $noticias = $pdo->query('SELECT * FROM blog ORDER BY fecha DESC')->fetchAll();
?>
    <div class="card" style="padding:0;">
        <table style="width:100%; border-collapse:collapse; text-align:left;">
            <thead>
                <tr style="background:#f8f9fa; border-bottom:2px solid #eee;">
                    <th style="padding:15px; width:80px;">Imagen</th>
                    <th style="padding:15px;">Título</th>
                    <th style="padding:15px;">Categoría</th>
                    <th style="padding:15px;">Fecha</th>
                    <th style="padding:15px; text-align:right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($noticias) > 0): ?>
                    <?php foreach ($noticias as $item): ?>
                    <tr style="border-bottom:1px solid #eee;">
                        <td style="padding:15px;">
                            <img src="../<?php echo htmlspecialchars($item['imagen']); ?>"
                                 style="width:60px; height:60px; object-fit:cover; border-radius:8px;">
                        </td>
                        <td style="padding:15px; font-weight:600; color:var(--klyp-dark);">
                            <?php echo htmlspecialchars($item['titulo']); ?>
                        </td>
                        <td style="padding:15px;">
                            <span style="background:#e1f5fe; color:#0288d1; padding:5px 10px; border-radius:20px; font-size:0.85rem; font-weight:600;">
                                <?php echo htmlspecialchars($item['tipo']); ?>
                            </span>
                        </td>
                        <td style="padding:15px; color:var(--text-gray);">
                            <?php echo date('d/m/Y', strtotime($item['fecha'])); ?>
                        </td>
                        <td style="padding:15px; text-align:right;">
                            <a href="blog.php?accion=editar&id=<?php echo $item['id']; ?>"
                               class="btn" style="padding:8px 15px; font-size:0.9rem;">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" style="display:inline;"
                                  onsubmit="return confirm('¿Estás seguro de que deseas borrar esta noticia?')">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="borrar" value="<?php echo $item['id']; ?>">
                                <button type="submit" class="btn" style="background:#e74c3c; padding:8px 15px; font-size:0.9rem;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="padding:30px; text-align:center; color:var(--text-gray);">
                            No hay noticias publicadas aún.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

</div></body></html>
