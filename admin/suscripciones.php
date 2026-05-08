<?php
// 1. Incluimos la cabecera (sesión y menú)
require_once 'header.php';

// 2. Conexión a la base de datos
require_once '../db.php'; 

// 3. Consulta SQL - Ahora con nombre_empresa
try {
    $sql = "SELECT s.id, s.ciclo, s.precio, s.estado, s.fecha_renovacion, 
                   c.nombre_empresa AS cliente_nombre, 
                   c.email AS cliente_email 
            FROM suscripciones s 
            LEFT JOIN clientes c ON s.cliente_id = c.id 
            ORDER BY s.fecha_renovacion ASC";
    
    $stmt = $pdo->query($sql); 
    $suscripciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $error = "Error al cargar las suscripciones: " . $e->getMessage();
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h2 style="margin: 0; color: #333;"><i class="fas fa-sync-alt"></i> Gestión de Suscripciones</h2>
    <a href="suscripcion_nueva.php" style="background-color: #0056b3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: 600; font-size: 0.9rem; transition: background 0.3s;">
        <i class="fas fa-plus"></i> Nueva Suscripción
    </a>
</div>

<?php if (isset($error)): ?>
    <div style="background: #f8d7da; color: #842029; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
    </div>
<?php endif; ?>

<div style="background: white; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead style="background-color: #f8f9fa; border-bottom: 2px solid #e9ecef;">
            <tr>
                <th style="padding: 15px 20px; color: #495057; font-weight: 600; font-size: 0.9rem;">Cliente / Empresa</th>
                <th style="padding: 15px 20px; color: #495057; font-weight: 600; font-size: 0.9rem;">Plan / Precio</th>
                <th style="padding: 15px 20px; color: #495057; font-weight: 600; font-size: 0.9rem;">Estado</th>
                <th style="padding: 15px 20px; color: #495057; font-weight: 600; font-size: 0.9rem;">Próxima Renovación</th>
                <th style="padding: 15px 20px; color: #495057; font-weight: 600; font-size: 0.9rem; text-align: center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            
            <?php if (!empty($suscripciones)): ?>
                <?php foreach ($suscripciones as $sub): ?>
                    <tr style="border-bottom: 1px solid #e9ecef; transition: background 0.2s;">
                        
                        <td style="padding: 15px 20px;">
                            <strong style="color: #333;"><?php echo htmlspecialchars($sub['cliente_nombre'] ?? 'Sin nombre'); ?></strong><br>
                            <span style="font-size: 0.8rem; color: #6c757d;"><?php echo htmlspecialchars($sub['cliente_email'] ?? '-'); ?></span>
                        </td>
                        
                        <td style="padding: 15px 20px; color: #555;">
                            <span style="text-transform: capitalize;"><?php echo htmlspecialchars($sub['ciclo']); ?></span><br>
                            <small style="font-weight: 600;"><?php echo number_format($sub['precio'], 2, ',', '.'); ?>€</small>
                        </td>
                        
                        <td style="padding: 15px 20px;">
                            <?php 
                            $estado = strtolower($sub['estado']);
                            if ($estado == 'activa' || $estado == 'activo'): ?>
                                <span style="background-color: #d1e7dd; color: #0f5132; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">Activa</span>
                            <?php elseif ($estado == 'cancelada' || $estado == 'cancelado'): ?>
                                <span style="background-color: #f8d7da; color: #842029; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">Cancelada</span>
                            <?php else: ?>
                                <span style="background-color: #fff3cd; color: #664d03; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;"><?php echo htmlspecialchars($sub['estado']); ?></span>
                            <?php endif; ?>
                        </td>
                        
                        <td style="padding: 15px 20px; color: #555;">
                            <?php echo !empty($sub['fecha_renovacion']) ? date('d/m/Y', strtotime($sub['fecha_renovacion'])) : '-'; ?>
                        </td>
                        
                        <td style="padding: 15px 20px; text-align: center;">
                            <a href="suscripcion_editar.php?id=<?php echo $sub['id']; ?>" style="color: #0d6efd; margin-right: 15px; text-decoration: none;" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="suscripcion_eliminar.php" style="display:inline;"
                                  onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta suscripción?')">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="id" value="<?php echo $sub['id']; ?>">
                                <button type="submit" style="background:none; border:none; color:#dc3545; cursor:pointer;" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="padding: 40px; text-align: center; color: #999;">
                        No se encontraron suscripciones activas.
                    </td>
                </tr>
            <?php endif; ?>

        </tbody>
    </table>
</div>

    </div> </body>
</html>