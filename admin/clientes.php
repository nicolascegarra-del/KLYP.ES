<?php require 'header.php'; require '../db.php';

// Borrar cliente — solo por POST con CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    csrf_verify();
    $pdo->prepare('DELETE FROM clientes WHERE id = ?')->execute([(int)$_POST['delete']]);
    header('Location: clientes.php?msg=borrado');
    exit;
}

$mensaje = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'borrado') $mensaje = 'Cliente eliminado permanentemente.';
    if ($_GET['msg'] === 'creado')  $mensaje = 'Nuevo cliente dado de alta correctamente.';
}

$search = $_GET['search'] ?? '';
if ($search) {
    $stmt = $pdo->prepare('SELECT * FROM clientes WHERE nombre_empresa LIKE ? OR email LIKE ? OR cif LIKE ? ORDER BY fecha_registro DESC');
    $stmt->execute(["%$search%", "%$search%", "%$search%"]);
    $clientes = $stmt->fetchAll();
} else {
    $clientes = $pdo->query('SELECT * FROM clientes ORDER BY fecha_registro DESC')->fetchAll();
}
?>

<div class="header" style="display:flex; justify-content:space-between; align-items:center;">
    <h1 class="page-title">Directorio de Clientes</h1>
    <a href="cliente_editar.php" class="btn" style="background-color:var(--klyp-blue); color:white; text-decoration:none; padding:10px 20px; border-radius:5px; font-weight:600;">
        <i class="fas fa-user-plus"></i> Nuevo Cliente
    </a>
</div>

<?php if ($mensaje): ?>
    <div style="background:#d4edda; color:#155724; padding:15px; border-radius:8px; margin-bottom:20px;">
        <i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<div class="card" style="margin-bottom:20px; padding:15px 25px;">
    <form method="GET" style="display:flex; gap:10px;">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
               placeholder="Buscar por nombre, CIF o email..."
               style="flex:1; padding:10px; border:1px solid #ddd; border-radius:5px;">
        <button type="submit" class="btn" style="padding:10px 20px;"><i class="fas fa-search"></i> Buscar</button>
        <?php if ($search): ?>
            <a href="clientes.php" class="btn" style="background:#f1f1f1; color:#333; text-decoration:none; padding:10px 20px;">Limpiar</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Empresa / Cliente</th>
                <th>Email Acceso</th>
                <th>Estado</th>
                <th>Fecha Alta</th>
                <th style="text-align:center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($clientes) > 0): ?>
                <?php foreach ($clientes as $c): ?>
                <tr>
                    <td style="color:var(--text-gray);">#<?php echo $c['id']; ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($c['nombre_empresa']); ?></strong>
                        <?php if ($c['cif']): ?>
                            <br><small style="color:#888;">CIF: <?php echo htmlspecialchars($c['cif']); ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($c['email']); ?></td>
                    <td>
                        <?php
                        $badge_color = '#6c757d';
                        if ($c['estado'] === 'Activo')    $badge_color = '#28a745';
                        if ($c['estado'] === 'Moroso')    $badge_color = '#dc3545';
                        if ($c['estado'] === 'En Pruebas') $badge_color = '#ffc107';
                        $text_color = ($c['estado'] === 'En Pruebas') ? '#333' : '#fff';
                        ?>
                        <span style="background-color:<?php echo $badge_color; ?>; color:<?php echo $text_color; ?>; padding:5px 12px; border-radius:20px; font-size:0.85rem; font-weight:600;">
                            <?php echo htmlspecialchars($c['estado']); ?>
                        </span>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($c['fecha_registro'])); ?></td>
                    <td style="text-align:center;">
                        <a href="cliente_editar.php?id=<?php echo $c['id']; ?>"
                           class="btn-edit" style="color:var(--klyp-blue); text-decoration:none; margin-right:15px;" title="Ver Ficha">
                            <i class="fas fa-edit"></i> Ficha
                        </a>
                        <form method="POST" style="display:inline;"
                              onsubmit="return confirm('ATENCIÓN: Esto borrará el cliente definitivamente. ¿Deseas continuar?')">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="delete" value="<?php echo $c['id']; ?>">
                            <button type="submit" style="background:none; border:none; color:#dc3545; cursor:pointer; font-size:0.9rem;" title="Borrar Definitivamente">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; color:#888; padding:30px;">
                        No se han encontrado clientes.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</div></body></html>
