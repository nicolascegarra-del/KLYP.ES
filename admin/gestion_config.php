<?php require 'header.php'; require '../db.php';

// Eliminar un email de departamento — solo por POST con CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['del_email'])) {
    csrf_verify();
    $pdo->prepare('DELETE FROM klyp_emails_departamentos WHERE id = ?')->execute([(int)$_POST['del_email']]);
    header('Location: gestion_config.php?msg=borrado');
    exit;
}

if (isset($_GET['msg']) && $_GET['msg'] === 'borrado') {
    $mensaje = 'Email eliminado correctamente.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    if (isset($_POST['action']) && $_POST['action'] === 'fiscal') {
        $pdo->prepare('UPDATE configuracion SET razon_social=?, cif=?, direccion_fiscal=?, cp_fiscal=?, poblacion_fiscal=?, provincia_fiscal=?, telefono_fiscal=? WHERE id=1')
            ->execute([
                $_POST['razon_social'],
                $_POST['cif'],
                $_POST['direccion_fiscal'],
                $_POST['cp_fiscal'],
                $_POST['poblacion_fiscal'],
                $_POST['provincia_fiscal'],
                $_POST['telefono_fiscal'],
            ]);
        $mensaje = '¡Datos fiscales guardados correctamente!';
    } elseif (isset($_POST['action']) && $_POST['action'] === 'add_email') {
        $pdo->prepare('INSERT INTO klyp_emails_departamentos (departamento, email) VALUES (?, ?)')
            ->execute([$_POST['nuevo_departamento'], $_POST['nuevo_email']]);
        $mensaje = '¡Nuevo departamento añadido con éxito!';
    }
}

$config      = $pdo->query('SELECT * FROM configuracion WHERE id=1')->fetch();
$emails_dptos = $pdo->query('SELECT * FROM klyp_emails_departamentos ORDER BY departamento ASC')->fetchAll();
?>

<div class="header">
    <h1 class="page-title">Gestión Administrativa <span style="color:var(--text-gray); font-size:1.2rem;">/ Datos Fiscales</span></h1>
</div>

<?php if (isset($mensaje)): ?>
    <div style="background:#d4edda; color:#155724; padding:15px; border-radius:8px; margin-bottom:20px;">
        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<div class="card" style="margin-bottom:30px;">
    <form method="POST">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="action" value="fiscal">
        <h3 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:10px; color:var(--klyp-dark);">Datos de Facturación de KLYP</h3>
        <p style="color:var(--text-gray); font-size:0.95rem; margin-bottom:25px;">Estos datos aparecerán como emisor en los PDF de las facturas.</p>

        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; margin-bottom:20px;">
            <div class="form-group">
                <label>Razón Social / Comercial:</label>
                <input type="text" name="razon_social" value="<?php echo htmlspecialchars($config['razon_social'] ?? ''); ?>" placeholder="Ej: KLYP S.L.">
            </div>
            <div class="form-group">
                <label>CIF / NIF:</label>
                <input type="text" name="cif" value="<?php echo htmlspecialchars($config['cif'] ?? ''); ?>" placeholder="Ej: B-12345678">
            </div>
            <div class="form-group">
                <label>Teléfono (Fiscal/Principal):</label>
                <input type="text" name="telefono_fiscal" value="<?php echo htmlspecialchars($config['telefono_fiscal'] ?? ''); ?>" placeholder="Ej: +34 900 123 456">
            </div>
        </div>

        <div class="form-group" style="margin-bottom:20px;">
            <label>Dirección Fiscal completa:</label>
            <input type="text" name="direccion_fiscal" value="<?php echo htmlspecialchars($config['direccion_fiscal'] ?? ''); ?>" placeholder="Calle, número, piso...">
        </div>

        <div style="display:grid; grid-template-columns:1fr 2fr 2fr; gap:20px; margin-bottom:30px;">
            <div class="form-group">
                <label>Código Postal:</label>
                <input type="text" name="cp_fiscal" value="<?php echo htmlspecialchars($config['cp_fiscal'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Población:</label>
                <input type="text" name="poblacion_fiscal" value="<?php echo htmlspecialchars($config['poblacion_fiscal'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Provincia:</label>
                <input type="text" name="provincia_fiscal" value="<?php echo htmlspecialchars($config['provincia_fiscal'] ?? ''); ?>">
            </div>
        </div>

        <div style="text-align:right;">
            <button type="submit" class="btn" style="padding:12px 30px; font-size:1.1rem; background-color:var(--klyp-dark); color:white;">
                Guardar Datos Fiscales
            </button>
        </div>
    </form>
</div>

<div class="card">
    <h3 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:10px; color:var(--klyp-blue);">Contactos y Departamentos (Routing)</h3>
    <p style="color:var(--text-gray); font-size:0.95rem; margin-bottom:20px;">Añade los distintos correos electrónicos de la empresa para la gestión interna y notificaciones.</p>

    <form method="POST" style="display:flex; gap:15px; margin-bottom:30px; align-items:flex-end; background:#f9fbfd; padding:20px; border-radius:10px; border:1px dashed #cce7ff;">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="action" value="add_email">
        <div class="form-group" style="flex:1; margin-bottom:0;">
            <label>Nombre del Departamento:</label>
            <input type="text" name="nuevo_departamento" required placeholder="Ej: Facturación, Soporte, DPO...">
        </div>
        <div class="form-group" style="flex:1; margin-bottom:0;">
            <label>Email asignado:</label>
            <input type="email" name="nuevo_email" required placeholder="Ej: dpo@klyp.es">
        </div>
        <button type="submit" class="btn" style="background-color:var(--klyp-blue); color:white; padding:10px 20px;">
            <i class="fas fa-plus"></i> Añadir
        </button>
    </form>

    <table class="data-table">
        <thead>
            <tr>
                <th>Departamento</th>
                <th>Correo Electrónico</th>
                <th style="text-align:center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($emails_dptos) > 0): ?>
                <?php foreach ($emails_dptos as $email): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($email['departamento']); ?></strong></td>
                    <td><?php echo htmlspecialchars($email['email']); ?></td>
                    <td style="text-align:center;">
                        <form method="POST" style="display:inline;"
                              onsubmit="return confirm('¿Seguro que quieres borrar el email de <?php echo htmlspecialchars(addslashes($email['departamento'])); ?>?')">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="del_email" value="<?php echo $email['id']; ?>">
                            <button type="submit" class="btn-delete"
                                    style="color:#dc3545; background:none; border:1px solid #dc3545; padding:5px 10px; border-radius:4px; cursor:pointer;">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="text-align:center; color:#888;">No hay correos de departamento configurados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</div></body></html>
