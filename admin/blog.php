<?php
require 'header.php';
require '../db.php';

const UPLOAD_DIR       = '../assets/img/blog/';
const MAX_UPLOAD_BYTES = 2 * 1024 * 1024; // 2 MB
const ALLOWED_MIME     = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
const ALLOWED_EXT      = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

// ── Borrar noticia ───────────────────────────────────────────────────────────
if (isset($_GET['borrar'])) {
    csrf_verify();
    $id_borrar = (int)$_GET['borrar'];

    $stmt = $pdo->prepare("SELECT imagen FROM blog WHERE id = ?");
    $stmt->execute([$id_borrar]);
    $noticia = $stmt->fetch();

    if ($noticia) {
        $ruta_fisica = realpath('../' . $noticia['imagen']);
        $base_dir    = realpath('../assets/img/blog');
        if ($ruta_fisica && $base_dir && strpos($ruta_fisica, $base_dir) === 0) {
            @unlink($ruta_fisica);
        }
        $pdo->prepare("DELETE FROM blog WHERE id = ?")->execute([$id_borrar]);
    }

    $_SESSION['flash'] = 'Noticia eliminada.';
    header('Location: blog.php');
    exit;
}

// ── Guardar / editar noticia ─────────────────────────────────────────────────
$errores = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_noticia'])) {
    csrf_verify();

    $titulo = trim($_POST['titulo'] ?? '');
    $texto  = trim($_POST['texto']  ?? '');
    $fecha  = trim($_POST['fecha']  ?? '');
    $tipo   = trim($_POST['tipo']   ?? '');
    $id     = (int)($_POST['id']    ?? 0);

    if (strlen($titulo) < 3) $errores[] = 'El título es demasiado corto.';
    if (strlen($texto) < 10) $errores[] = 'El contenido es demasiado corto.';
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) $errores[] = 'Fecha inválida.';

    // Ruta de imagen actual (validada contra path traversal)
    $ruta_imagen = '';
    if (!empty($_POST['imagen_actual'])) {
        $posible = realpath('../' . $_POST['imagen_actual']);
        $base    = realpath('../assets/img/blog');
        if ($posible && $base && strpos($posible, $base) === 0) {
            $ruta_imagen = $_POST['imagen_actual'];
        }
    }

    // Procesar nueva imagen subida
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['imagen'];

        // Validar tamaño
        if ($file['size'] > MAX_UPLOAD_BYTES) {
            $errores[] = 'La imagen no puede superar los 2 MB.';
        }

        // Validar MIME real (no el del cliente)
        $finfo     = new finfo(FILEINFO_MIME_TYPE);
        $mime_real = $finfo->file($file['tmp_name']);
        if (!in_array($mime_real, ALLOWED_MIME, true)) {
            $errores[] = 'Tipo de archivo no permitido. Solo se aceptan imágenes (JPG, PNG, GIF, WEBP).';
        }

        // Validar extensión
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ALLOWED_EXT, true)) {
            $errores[] = 'Extensión de archivo no permitida.';
        }

        if (empty($errores)) {
            $nombre_seguro = bin2hex(random_bytes(16)) . '.' . $ext;
            $destino       = UPLOAD_DIR . $nombre_seguro;

            if (move_uploaded_file($file['tmp_name'], $destino)) {
                // Borrar imagen anterior si existe
                if ($ruta_imagen) {
                    $vieja = realpath('../' . $ruta_imagen);
                    $base  = realpath('../assets/img/blog');
                    if ($vieja && $base && strpos($vieja, $base) === 0) {
                        @unlink($vieja);
                    }
                }
                $ruta_imagen = 'assets/img/blog/' . $nombre_seguro;
            } else {
                $errores[] = 'Error al guardar la imagen.';
            }
        }
    }

    if (empty($errores)) {
        if ($id === 0) {
            if (!$ruta_imagen) $errores[] = 'La imagen es obligatoria para nuevas publicaciones.';
            else {
                $pdo->prepare("INSERT INTO blog (titulo, texto, imagen, fecha, tipo) VALUES (?,?,?,?,?)")
                    ->execute([$titulo, $texto, $ruta_imagen, $fecha, $tipo]);
                $_SESSION['flash'] = 'Noticia publicada con éxito.';
                header('Location: blog.php');
                exit;
            }
        } else {
            $pdo->prepare("UPDATE blog SET titulo=?, texto=?, imagen=?, fecha=?, tipo=? WHERE id=?")
                ->execute([$titulo, $texto, $ruta_imagen, $fecha, $tipo, $id]);
            $_SESSION['flash'] = 'Noticia actualizada con éxito.';
            header('Location: blog.php');
            exit;
        }
    }
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$apps = $pdo->query("SELECT id, titulo FROM aplicaciones")->fetchAll();
?>

<div class="header" style="display:flex; justify-content:space-between; align-items:center;">
    <h1 class="page-title">Blog y Novedades</h1>
    <?php if (!isset($_GET['accion'])): ?>
        <a href="blog.php?accion=nuevo" class="btn" style="background:var(--klyp-blue);">+ Nueva Publicación</a>
    <?php endif; ?>
</div>

<?php if ($flash): ?>
    <div style="background:#d4edda; color:#155724; padding:15px; border-radius:8px; margin-bottom:20px;"><?php echo htmlspecialchars($flash); ?></div>
<?php endif; ?>

<?php if (!empty($errores)): ?>
    <div style="background:#f8d7da; color:#721c24; padding:15px; border-radius:8px; margin-bottom:20px;">
        <?php foreach ($errores as $e): ?><div>• <?php echo htmlspecialchars($e); ?></div><?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
// ── Vista formulario crear/editar ────────────────────────────────────────────
if (isset($_GET['accion']) && in_array($_GET['accion'], ['nuevo', 'editar'])):
    $n = ['id'=>'', 'titulo'=>'', 'texto'=>'', 'imagen'=>'', 'fecha'=>date('Y-m-d'), 'tipo'=>'Corporativa'];
    if ($_GET['accion'] === 'editar' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM blog WHERE id = ?");
        $stmt->execute([(int)$_GET['id']]);
        $n = $stmt->fetch() ?: $n;
    }
?>
<div class="card">
    <h3 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:10px;">
        <?php echo ($_GET['accion'] === 'nuevo') ? 'Redactar Nueva Noticia' : 'Editar Noticia'; ?>
    </h3>
    <form method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="guardar_noticia" value="1">
        <input type="hidden" name="id" value="<?php echo (int)$n['id']; ?>">
        <input type="hidden" name="imagen_actual" value="<?php echo htmlspecialchars($n['imagen']); ?>">

        <div style="display:grid; grid-template-columns:2fr 1fr; gap:20px;">
            <div class="form-group">
                <label>Título:</label>
                <input type="text" name="titulo" value="<?php echo htmlspecialchars($n['titulo']); ?>" required minlength="3">
            </div>
            <div class="form-group">
                <label>Fecha:</label>
                <input type="date" name="fecha" value="<?php echo htmlspecialchars($n['fecha']); ?>" required>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
            <div class="form-group">
                <label>Categoría:</label>
                <select name="tipo" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-family:'Poppins',sans-serif;">
                    <option value="Corporativa" <?php echo ($n['tipo']==='Corporativa')?'selected':''; ?>>🏢 Noticia Corporativa</option>
                    <optgroup label="Referente a una App:">
                        <?php foreach ($apps as $app): ?>
                            <option value="<?php echo htmlspecialchars($app['titulo']); ?>"
                                <?php echo ($n['tipo']===$app['titulo'])?'selected':''; ?>>
                                📱 App: <?php echo htmlspecialchars($app['titulo']); ?>
                            </option>
                        <?php endforeach; ?>
                    </optgroup>
                </select>
            </div>
            <div class="form-group">
                <label>Imagen Destacada (máx. 2 MB, JPG/PNG/GIF/WEBP):</label>
                <input type="file" name="imagen" accept="image/jpeg,image/png,image/gif,image/webp"
                    <?php echo ($_GET['accion']==='nuevo')?'required':''; ?> style="padding:5px;">
                <?php if ($n['imagen']): ?>
                    <small style="display:block; margin-top:5px; color:var(--text-gray);">
                        Imagen actual: <img src="../<?php echo htmlspecialchars($n['imagen']); ?>"
                            style="height:30px; vertical-align:middle; border-radius:4px; margin-left:5px;">
                    </small>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <label>Contenido:</label>
            <textarea name="texto" rows="8" required minlength="10"
                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-family:'Poppins',sans-serif; resize:vertical;"><?php echo htmlspecialchars($n['texto']); ?></textarea>
        </div>

        <div style="margin-top:20px; display:flex; gap:10px; justify-content:flex-end;">
            <a href="blog.php" class="btn" style="background:#e74c3c;">Cancelar</a>
            <button type="submit" class="btn" style="background:#2ecc71;">Guardar Noticia</button>
        </div>
    </form>
</div>

<?php else:
// ── Vista listado ────────────────────────────────────────────────────────────
$noticias = $pdo->query("SELECT * FROM blog ORDER BY fecha DESC")->fetchAll();
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
                             alt="<?php echo htmlspecialchars($item['titulo']); ?>"
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
                        <a href="blog.php?accion=editar&id=<?php echo (int)$item['id']; ?>"
                           class="btn" style="padding:8px 15px; font-size:0.9rem;"><i class="fas fa-edit"></i></a>
                        <a href="blog.php?borrar=<?php echo (int)$item['id']; ?>&_token=<?php echo csrf_token(); ?>"
                           class="btn" style="background:#e74c3c; padding:8px 15px; font-size:0.9rem;"
                           onclick="return confirm('¿Borrar esta noticia?')"><i class="fas fa-trash"></i></a>
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
