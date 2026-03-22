<?php
header('Content-Type: application/xml; charset=utf-8');
header('X-Robots-Tag: noindex');

require 'db.php';

$base = 'https://www.klyp.es';

$apps    = $pdo->query("SELECT id FROM aplicaciones")->fetchAll();
$blog    = $pdo->query("SELECT id, fecha FROM blog ORDER BY fecha DESC")->fetchAll();
?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <!-- Páginas estáticas -->
    <url>
        <loc><?php echo $base; ?>/</loc>
        <changefreq>monthly</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc><?php echo $base; ?>/blog.php</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>

    <!-- Aplicaciones -->
    <?php foreach ($apps as $app): ?>
    <url>
        <loc><?php echo $base; ?>/detalle_app.php?id=<?php echo (int)$app['id']; ?></loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <?php endforeach; ?>

    <!-- Artículos del blog -->
    <?php foreach ($blog as $post): ?>
    <url>
        <loc><?php echo $base; ?>/blog.php?id=<?php echo (int)$post['id']; ?></loc>
        <lastmod><?php echo htmlspecialchars($post['fecha']); ?></lastmod>
        <changefreq>never</changefreq>
        <priority>0.6</priority>
    </url>
    <?php endforeach; ?>

</urlset>
