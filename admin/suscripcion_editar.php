<?php
require_once 'header.php';
require_once '../db.php';

$mensaje = "";
$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    header('Location: suscripciones.php');
    exit;
}

// 1. PROCESAR LA ACTUALIZACIÓN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    try {
        $sql = "UPDATE suscripciones SET 
                cliente_id = :cliente_id, 
                precio = :precio, 
                ciclo = :ciclo, 
                fecha_alta = :fecha_alta, 
                fecha_renovacion = :fecha_renovacion, 
                estado = :estado 
                WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':cliente_id'       => $_POST['cliente_id'],
            ':precio'           => $_POST['precio'],
            ':ciclo'            => $_POST['ciclo'],
            ':fecha_alta'       => $_POST['fecha_alta'],
            ':fecha_renovacion' => $_POST['fecha_renovacion'],
            ':estado'           => $_POST['estado'],
            ':id'               => $id
        ]);

        $mensaje = "<div style='background: #d1e7dd; color: #0f5132; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #badbcc;'>¡Suscripción actualizada correctamente!</div>";
    } catch (Exception $e) {
        $mensaje = "<div style='background: #f8d7da; color: #842029; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;'>Error: " . $e->getMessage() . "</div>";
    }
}

// 2. OBTENER DATOS ACTUALES DE LA SUSCRIPCIÓN
$stmt = $pdo->prepare("SELECT * FROM suscripciones WHERE id = ?");
$stmt->execute([$id]);
$suscripcion = $stmt->fetch();

if (!$suscripcion) {
    die("Suscripción no encontrada.");
}

// 3. OBTENER CLIENTES PARA EL SELECTOR
$clientes = $pdo->query("SELECT id, nombre_empresa FROM clientes ORDER BY nombre_empresa ASC")->fetchAll();
?>

<div style="max-width: 800px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin: 0; color: #333;"><i class="fas fa-edit"></i> Editar Suscripción #<?php echo $id; ?></h2>
        <a href="suscripciones.php" style="color: #666; text-decoration: none;"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>

    <?php echo $mensaje; ?>

    <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <form method="POST" action="">
            <?php echo csrf_field(); ?>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                
                <div style="grid-column: span 2;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Cliente</label>
                    <select name="cliente_id" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px;">
                        <?php foreach ($clientes as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo ($c['id'] == $suscripcion['cliente_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['nombre_empresa']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Ciclo de Facturación</label>
                    <select name="ciclo" id="ciclo" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="mensual" <?php echo ($suscripcion['ciclo'] == 'mensual') ? 'selected' : ''; ?>>Mensual</option>
                        <option value="trimestral" <?php echo ($suscripcion['ciclo'] == 'trimestral') ? 'selected' : ''; ?>>Trimestral</option>
                        <option value="anual" <?php echo ($suscripcion['ciclo'] == 'anual') ? 'selected' : ''; ?>>Anual</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Precio (€)</label>
                    <input type="number" step="0.01" name="precio" value="<?php echo $suscripcion['precio']; ?>" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Fecha de Alta</label>
                    <input type="date" name="fecha_alta" id="fecha_alta" value="<?php echo $suscripcion['fecha_alta']; ?>" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Próxima Renovación</label>
                    <input type="date" name="fecha_renovacion" id="fecha_renovacion" value="<?php echo $suscripcion['fecha_renovacion']; ?>" required style="width: 100%; padding: 12px; border: 1px solid #0056b3; border-radius: 4px; background: #f0f7ff;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Estado</label>
                    <select name="estado" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="Activa" <?php echo ($suscripcion['estado'] == 'Activa') ? 'selected' : ''; ?>>Activa</option>
                        <option value="Pendiente" <?php echo ($suscripcion['estado'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="Cancelada" <?php echo ($suscripcion['estado'] == 'Cancelada') ? 'selected' : ''; ?>>Cancelada</option>
                    </select>
                </div>

            </div>

            <div style="margin-top: 30px; text-align: right; border-top: 1px solid #eee; padding-top: 20px;">
                <button type="submit" style="background: #28a745; color: white; padding: 12px 35px; border: none; border-radius: 5px; font-weight: 600; cursor: pointer; font-size: 1rem;">
                    Actualizar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// El mismo script que en nueva suscripción para mantener la coherencia
document.addEventListener('DOMContentLoaded', function() {
    const fechaAltaInput = document.getElementById('fecha_alta');
    const cicloSelect = document.getElementById('ciclo');
    const fechaRenovacionInput = document.getElementById('fecha_renovacion');

    function calcularRenovacion() {
        let fechaAlta = new Date(fechaAltaInput.value);
        if (isNaN(fechaAlta.getTime())) return;
        let ciclo = cicloSelect.value;

        if (ciclo === 'mensual') fechaAlta.setMonth(fechaAlta.getMonth() + 1);
        else if (ciclo === 'trimestral') fechaAlta.setMonth(fechaAlta.getMonth() + 3);
        else if (ciclo === 'anual') fechaAlta.setFullYear(fechaAlta.getFullYear() + 1);

        let y = fechaAlta.getFullYear();
        let m = ('0' + (fechaAlta.getMonth() + 1)).slice(-2);
        let d = ('0' + fechaAlta.getDate()).slice(-2);
        fechaRenovacionInput.value = `${y}-${m}-${d}`;
    }

    fechaAltaInput.addEventListener('change', calcularRenovacion);
    cicloSelect.addEventListener('change', calcularRenovacion);
});
</script>

    </div>
</body>
</html>