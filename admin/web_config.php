<?php require 'header.php'; require '../db.php'; 

// Guardar cambios
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_verify();
    $sql = "UPDATE configuracion SET email_contacto=?, instagram=?, linkedin=?, facebook=?, twitter=?, num_noticias_landing=?, posicion_noticias=?, texto_nosotros=?, texto_hacemos=?, modo_desarrollo=? WHERE id=1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['email'],
        $_POST['insta'],
        $_POST['linkedin'],
        $_POST['fb'],
        $_POST['tw'],
        $_POST['num_noticias'],
        $_POST['posicion_noticias'],
        $_POST['texto_nosotros'],
        $_POST['texto_hacemos'],
        isset($_POST['modo_desarrollo']) ? 1 : 0,
    ]);
    $mensaje = "¡Configuración guardada correctamente!";
}

// Obtener datos
$config = $pdo->query("SELECT * FROM configuracion WHERE id=1")->fetch();
?>

<div class="header">
    <h1 class="page-title">Configuración Web</h1>
</div>

<?php if(isset($mensaje)) echo "<div style='background:#d4edda; color:#155724; padding:15px; border-radius:8px; margin-bottom:20px;'>$mensaje</div>"; ?>

<div class="card">
    <form method="POST">
        <?php echo csrf_field(); ?>
        <h3 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:10px;">Textos de la Portada (Landing)</h3>
        
        <div class="form-group" style="margin-bottom: 20px;">
            <label>Texto sección "¿Quiénes somos?":</label>
            <textarea name="texto_nosotros" rows="4" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Poppins', sans-serif; resize: vertical;"><?php echo htmlspecialchars($config['texto_nosotros'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label>Texto sección "¿Qué hacemos?":</label>
            <textarea name="texto_hacemos" rows="4" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Poppins', sans-serif; resize: vertical;"><?php echo htmlspecialchars($config['texto_hacemos'] ?? ''); ?></textarea>
        </div>

        <h3 style="margin-top:30px; border-bottom:1px solid #eee; padding-bottom:10px;">Formulario de Contacto</h3>
        <div class="form-group">
            <label>Email donde recibirás los mensajes:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($config['email_contacto']); ?>" required>
            <small style="color:var(--text-gray)">Todos los mensajes del formulario 'Contacto' de la web se enviarán aquí.</small>
        </div>

        <h3 style="margin-top:30px; border-bottom:1px solid #eee; padding-bottom:10px;">Escaparate del Blog (Página Principal)</h3>
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
            <div class="form-group">
                <label>Número de noticias a mostrar:</label>
                <input type="number" name="num_noticias" value="<?php echo htmlspecialchars($config['num_noticias_landing'] ?? 3); ?>" min="1" max="12" required>
                <small style="color:var(--text-gray)">Cantidad de tarjetas de noticias que se verán en el inicio.</small>
            </div>
            
            <div class="form-group">
                <label>Posición en la página principal:</label>
                <select name="posicion_noticias" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Poppins', sans-serif;">
                    <option value="sobre_apps" <?php echo (($config['posicion_noticias'] ?? '') == 'sobre_apps') ? 'selected' : ''; ?>>Por encima de las App's</option>
                    <option value="sobre_nosotros" <?php echo (($config['posicion_noticias'] ?? '') == 'sobre_nosotros') ? 'selected' : ''; ?>>Por encima de "¿Quiénes somos?"</option>
                    <option value="sobre_hacemos" <?php echo (($config['posicion_noticias'] ?? '') == 'sobre_hacemos') ? 'selected' : ''; ?>>Por encima de "¿Qué hacemos?"</option>
                    <option value="sobre_contacto" <?php echo (($config['posicion_noticias'] ?? '') == 'sobre_contacto') ? 'selected' : ''; ?>>Por encima del formulario de "Contacto"</option>
                    <option value="sobre_footer" <?php echo (($config['posicion_noticias'] ?? '') == 'sobre_footer') ? 'selected' : ''; ?>>Por encima del pie de página (Redes)</option>
                </select>
                <small style="color:var(--text-gray)">Elige en qué parte exacta de la web se inyectará el bloque de noticias.</small>
            </div>
        </div>

        <h3 style="margin-top:30px; border-bottom:1px solid #eee; padding-bottom:10px;">Redes Sociales</h3>
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
            <div class="form-group">
                <label><i class="fab fa-instagram"></i> Instagram (URL)</label>
                <input type="text" name="insta" value="<?php echo htmlspecialchars($config['instagram']); ?>" placeholder="https://instagram.com/...">
            </div>
            <div class="form-group">
                <label><i class="fab fa-linkedin"></i> LinkedIn (URL)</label>
                <input type="text" name="linkedin" value="<?php echo htmlspecialchars($config['linkedin']); ?>" placeholder="https://linkedin.com/...">
            </div>
            <div class="form-group">
                <label><i class="fab fa-facebook"></i> Facebook (URL)</label>
                <input type="text" name="fb" value="<?php echo htmlspecialchars($config['facebook']); ?>">
            </div>
            <div class="form-group">
                <label><i class="fab fa-twitter"></i> Twitter / X (URL)</label>
                <input type="text" name="tw" value="<?php echo htmlspecialchars($config['twitter']); ?>">
            </div>
        </div>

        <div style="margin-top:30px; padding:20px; background:#fff8e1; border:1px solid #ffe082; border-radius:10px; display:flex; align-items:center; gap:15px;">
            <label style="display:flex; align-items:center; gap:12px; cursor:pointer; margin:0;">
                <input type="checkbox" name="modo_desarrollo" value="1"
                       <?php echo !empty($config['modo_desarrollo']) ? 'checked' : ''; ?>
                       style="width:20px; height:20px; cursor:pointer; accent-color:#e67e22;">
                <div>
                    <strong style="color:#e67e22; font-size:1rem;">🚧 Página en Desarrollo</strong>
                    <p style="margin:4px 0 0; color:#888; font-size:0.85rem;">
                        Activa esta opción para mostrar una página de "Próximamente" a los visitantes mientras trabajas en la web. El panel admin seguirá siendo accesible.
                    </p>
                </div>
            </label>
        </div>

        <div style="margin-top: 20px; text-align: right;">
            <button type="submit" class="btn">Guardar Configuración</button>
        </div>
    </form>
</div>

</div></body></html>