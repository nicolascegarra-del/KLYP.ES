<?php
/**
 * Footer compartido para todas las páginas públicas.
 * Requiere que $config esté disponible en el contexto que lo incluye.
 * Opcionalmente acepta $mostrar_legales (bool) para mostrar links legales.
 */
$mostrar_legales = $mostrar_legales ?? false;
?>
<footer>
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> KLYP. Todos los derechos reservados.</p>
        <div class="social-links">
            <?php if (!empty($config['twitter'])): ?>
                <a href="<?php echo htmlspecialchars($config['twitter']); ?>" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-twitter"></i>
                </a>
            <?php endif; ?>
            <?php if (!empty($config['instagram'])): ?>
                <a href="<?php echo htmlspecialchars($config['instagram']); ?>" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-instagram"></i>
                </a>
            <?php endif; ?>
            <?php if (!empty($config['linkedin'])): ?>
                <a href="<?php echo htmlspecialchars($config['linkedin']); ?>" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-linkedin"></i>
                </a>
            <?php endif; ?>
            <?php if (!empty($config['facebook'])): ?>
                <a href="<?php echo htmlspecialchars($config['facebook']); ?>" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-facebook"></i>
                </a>
            <?php endif; ?>
        </div>
        <?php if ($mostrar_legales): ?>
        <div style="margin-top:15px; font-size:0.85rem; display:flex; justify-content:center; gap:20px; flex-wrap:wrap;">
            <a href="legal.php?id=aviso"      style="color:rgba(255,255,255,0.7); text-decoration:none;">Aviso Legal</a>
            <a href="legal.php?id=privacidad" style="color:rgba(255,255,255,0.7); text-decoration:none;">Política de Privacidad</a>
            <a href="legal.php?id=cookies"    style="color:rgba(255,255,255,0.7); text-decoration:none;">Política de Cookies</a>
        </div>
        <?php endif; ?>
    </div>
</footer>
