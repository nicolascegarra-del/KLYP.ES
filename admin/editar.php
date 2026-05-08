<?php
require_once 'security.php';
session_secure_start();
if (!isset($_SESSION['usuario_id'])) { header('Location: login.php'); exit; }

require '../db.php';
require 'funciones.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: apps.php'); exit; }

$stmt = $pdo->prepare('SELECT * FROM aplicaciones WHERE id = ?');
$stmt->execute([$id]);
$app = $stmt->fetch();
if (!$app) { header('Location: apps.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $pdo->prepare('UPDATE aplicaciones SET titulo=?, descripcion=?, contenido_extendido=?, icono=?, enlace=? WHERE id=?')
        ->execute([
            $_POST['titulo'],
            $_POST['descripcion'],
            $_POST['contenido_extendido'],
            $_POST['icono'],
            $_POST['enlace'],
            $id,
        ]);
    header('Location: apps.php');
    exit;
}

$iconos = getIcons();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Aplicación | KLYP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <div class="main-content" style="padding:40px;">
        <div class="card" style="max-width:800px; margin:0 auto;">
            <h2 class="page-title">Editar Aplicación</h2>
            <form method="POST">
                <?php echo csrf_field(); ?>
                <div class="form-group">
                    <label>Título</label>
                    <input type="text" name="titulo" value="<?php echo htmlspecialchars($app['titulo']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Descripción Corta (Tarjeta)</label>
                    <textarea name="descripcion" rows="2" required><?php echo htmlspecialchars($app['descripcion']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Contenido Detallado (+ info)</label>
                    <textarea name="contenido_extendido" rows="10"><?php echo htmlspecialchars($app['contenido_extendido']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Enlace Externo</label>
                    <input type="text" name="enlace" value="<?php echo htmlspecialchars($app['enlace']); ?>">
                </div>
                <div class="form-group">
                    <label>Icono</label>
                    <select name="icono">
                        <?php foreach ($iconos as $cat => $lista): ?>
                            <optgroup label="<?php echo htmlspecialchars($cat); ?>">
                                <?php foreach ($lista as $k => $v): ?>
                                    <option value="<?php echo htmlspecialchars($k); ?>"
                                        <?php echo ($app['icono'] === $k) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($v); ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn">Actualizar Aplicación</button>
                <a href="apps.php" style="margin-left:15px; color:#888;">Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>
