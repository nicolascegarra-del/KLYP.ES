<?php
require 'header.php';
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $sql = "UPDATE configuracion SET email_contacto=?, instagram=?, linkedin=?, facebook=?, twitter=?,
                num_noticias_landing=?, posicion_noticias=?, texto_nosotros=?, texto_hacemos=? WHERE id=1";
    $pdo->prepare($sql)->execute([
        trim($_POST['email']             ?? ''),
        trim($_POST['insta']             ?? ''),
        trim($_POST['linkedin']          ?? ''),
        trim($_POST['fb']                ?? ''),
        trim($_POST['tw']                ?? ''),
        (int)($_POST['num_noticias']     ?? 3),
        trim($_POST['posicion_noticias'] ?? 'sobre_contacto'),
        trim($_POST['texto_nosotros']    ?? ''),
        trim($_POST['texto_hacemos']     ?? ''),
    ]);
    $_SESSION['flash'] = '¡Configuración guardada correctamente!';
    header('Location: web_config.php');
    exit;
}

$config = $pdo->query("SELECT * FROM configuracion WHERE id=1")->fetch();
$flash  = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>

<div class="header">
    <h1 class="page-title">Configuración Web</h1>
</div>

<?php if ($flash): ?>
    <div style="background:#d4edda; color:#155724; padding:15px; border-radius:8px; margin-bottom:20px;">
        <?php echo htmlspecialchars($flash); ?>
    </div>
<?php endif; ?>

<div class="card">
    <form method="POST">
        <?php echo csrf_field(); ?>

        <h3 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:10px;">Textos de la Portada</h3>

        <div class="form-group" style="margin-bottom:20px;">
            <label>Texto sección "¿Quiénes somos?":</label>
            <textarea name="texto_nosotros" rows="4" required
                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-family:'Poppins',sans-serif; resize:vertical;"><?php echo htmlspecialchars($config['texto_nosotros'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label>Texto sección "¿Qué hacemos?":</label>
            <textarea name="texto_hacemos" rows="4" required
                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-family:'Poppins',sans-serif; resize:vertical;"><?php echo htmlspecialchars($config['texto_hacemos'] ?? ''); ?></textarea>
        </div>

        <h3 style="margin-top:30px; border-bottom:1px solid #eee; padding-bottom:10px;">Formulario de Contacto</h3>
        <div class="form-group">
            <label>Email donde recibirás los mensajes:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($config['email_contacto'] ?? ''); ?>" required>
            <small style="color:var(--text-gray)">Los mensajes del formulario de contacto se enviarán a esta dirección.</small>
        </div>

        <h3 style="margin-top:30px; border-bottom:1px solid #eee; padding-bottom:10px;">Escaparate del Blog</h3>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
            <div class="form-group">
                <label>Número de noticias a mostrar:</label>
                <input type="number" name="num_noticias" value="<?php echo (int)($config['num_noticias_landing'] ?? 3); ?>" min="1" max="12" required>
            </div>
            <div class="form-group">
                <label>Posición en la página principal:</label>
                <select name="posicion_noticias" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-family:'Poppins',sans-serif;">
                    <?php
                    $opciones = [
                        'sobre_apps'      => 'Por encima de las App\'s',
                        'sobre_nosotros'  => 'Por encima de "¿Quiénes somos?"',
                        'sobre_hacemos'   => 'Por encima de "¿Qué hacemos?"',
                        'sobre_contacto'  => 'Por encima del formulario de Contacto',
                        'sobre_footer'    => 'Por encima del pie de página',
                    ];
                    foreach ($opciones as $val => $label):
                        $sel = (($config['posicion_noticias'] ?? '') === $val) ? 'selected' : '';
                    ?>
                        <option value="<?php echo $val; ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <h3 style="margin-top:30px; border-bottom:1px solid #eee; padding-bottom:10px;">Redes Sociales</h3>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
            <div class="form-group">
                <label><i class="fab fa-instagram"></i> Instagram (URL)</label>
                <input type="url" name="insta" value="<?php echo htmlspecialchars($config['instagram'] ?? ''); ?>" placeholder="https://instagram.com/...">
            </div>
            <div class="form-group">
                <label><i class="fab fa-linkedin"></i> LinkedIn (URL)</label>
                <input type="url" name="linkedin" value="<?php echo htmlspecialchars($config['linkedin'] ?? ''); ?>" placeholder="https://linkedin.com/...">
            </div>
            <div class="form-group">
                <label><i class="fab fa-facebook"></i> Facebook (URL)</label>
                <input type="url" name="fb" value="<?php echo htmlspecialchars($config['facebook'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label><i class="fab fa-twitter"></i> Twitter / X (URL)</label>
                <input type="url" name="tw" value="<?php echo htmlspecialchars($config['twitter'] ?? ''); ?>">
            </div>
        </div>

        <div style="margin-top:20px; text-align:right;">
            <button type="submit" class="btn">Guardar Configuración</button>
        </div>
    </form>
</div>

</div></body></html>
