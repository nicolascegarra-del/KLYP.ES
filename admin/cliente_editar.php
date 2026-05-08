<?php 
require 'header.php'; 
require '../db.php'; 

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$mensaje = '';
$error = '';

// 1. ELIMINAR CONTACTO — solo por POST con CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['del_contacto']) && $id > 0) {
    csrf_verify();
    $pdo->prepare('DELETE FROM clientes_contactos WHERE id = ? AND cliente_id = ?')
        ->execute([(int)$_POST['del_contacto'], $id]);
    header("Location: cliente_editar.php?id=$id&msg=contacto_borrado");
    exit;
}

if (isset($_GET['msg']) && $_GET['msg'] == 'contacto_borrado') {
    $mensaje = "Contacto eliminado correctamente.";
}

// 2. PROCESAR FORMULARIOS (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    // A. GUARDAR / ACTUALIZAR DATOS BÁSICOS DEL CLIENTE
    if (isset($_POST['action']) && $_POST['action'] === 'save_cliente') {
        $nombre = $_POST['nombre_empresa'];
        $cif = $_POST['cif'];
        $email = $_POST['email'];
        $direccion = $_POST['direccion'];
        $cp = $_POST['cp'];
        $poblacion = $_POST['poblacion'];
        $provincia = $_POST['provincia'];
        $estado = $_POST['estado'];
        $password_plana = $_POST['password'];

        try {
            if ($id > 0) {
                // ACTUALIZAR EXISTENTE
                if (!empty($password_plana)) {
                    // Si escribe contraseña, la actualizamos encriptada
                    $hash = password_hash($password_plana, PASSWORD_DEFAULT);
                    $sql = "UPDATE clientes SET nombre_empresa=?, cif=?, email=?, password=?, direccion=?, cp=?, poblacion=?, provincia=?, estado=? WHERE id=?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$nombre, $cif, $email, $hash, $direccion, $cp, $poblacion, $provincia, $estado, $id]);
                } else {
                    // Si la deja en blanco, no tocamos la contraseña
                    $sql = "UPDATE clientes SET nombre_empresa=?, cif=?, email=?, direccion=?, cp=?, poblacion=?, provincia=?, estado=? WHERE id=?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$nombre, $cif, $email, $direccion, $cp, $poblacion, $provincia, $estado, $id]);
                }
                $mensaje = "Ficha de cliente actualizada correctamente.";
            } else {
                // CREAR NUEVO
                $hash = password_hash($password_plana, PASSWORD_DEFAULT); // Encriptamos obligatoriamente
                $sql = "INSERT INTO clientes (nombre_empresa, cif, email, password, direccion, cp, poblacion, provincia, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nombre, $cif, $email, $hash, $direccion, $cp, $poblacion, $provincia, $estado]);
                $nuevo_id = $pdo->lastInsertId();
                header("Location: cliente_editar.php?id=$nuevo_id&msg=creado");
                exit;
            }
        } catch (PDOException $e) {
            // Error típico: el email ya existe en otro cliente
            if ($e->getCode() == 23000) {
                $error = "Error: El email introducido ya está registrado en otro cliente.";
            } else {
                $error = "Error de base de datos: " . $e->getMessage();
            }
        }
    }
    
    // B. AÑADIR NUEVO CONTACTO/TELÉFONO
    if (isset($_POST['action']) && $_POST['action'] === 'add_contacto' && $id > 0) {
        $stmt = $pdo->prepare("INSERT INTO clientes_contactos (cliente_id, nombre_contacto, telefono) VALUES (?, ?, ?)");
        $stmt->execute([$id, $_POST['nombre_contacto'], $_POST['telefono']]);
        $mensaje = "Nuevo contacto añadido a la ficha.";
    }
}

if (isset($_GET['msg']) && $_GET['msg'] == 'creado') {
    $mensaje = "¡Cliente dado de alta! Ahora puedes añadirle teléfonos y asignarle App's.";
}

// 3. OBTENER DATOS (SI ESTAMOS EDITANDO)
$cliente = null;
$contactos = [];
$suscripciones = [];

if ($id > 0) {
    $cliente = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
    $cliente->execute([$id]);
    $cliente = $cliente->fetch();

    if (!$cliente) {
        echo "Cliente no encontrado."; exit;
    }

    // Obtener los contactos de este cliente
    $stmt = $pdo->prepare("SELECT * FROM clientes_contactos WHERE cliente_id = ? ORDER BY id ASC");
    $stmt->execute([$id]);
    $contactos = $stmt->fetchAll();

    // Obtener las suscripciones de este cliente cruzando datos con la tabla de aplicaciones
    $stmt = $pdo->prepare("SELECT s.*, a.titulo, a.icono FROM suscripciones s JOIN aplicaciones a ON s.aplicacion_id = a.id WHERE s.cliente_id = ? ORDER BY s.fecha_alta DESC");
    $stmt->execute([$id]);
    $suscripciones = $stmt->fetchAll();
}
?>

<div class="header" style="display: flex; justify-content: space-between; align-items: center;">
    <h1 class="page-title">
        <?php echo ($id > 0) ? 'Ficha de Cliente: <span style="color: var(--klyp-blue);">' . htmlspecialchars($cliente['nombre_empresa']) . '</span>' : 'Alta de Nuevo Cliente'; ?>
    </h1>
    <a href="clientes.php" class="btn" style="background-color: #6c757d; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px;">
        <i class="fas fa-arrow-left"></i> Volver al listado
    </a>
</div>

<?php if($mensaje): ?>
    <div style='background:#d4edda; color:#155724; padding:15px; border-radius:8px; margin-bottom:20px;'><i class='fas fa-check-circle'></i> <?php echo $mensaje; ?></div>
<?php endif; ?>
<?php if($error): ?>
    <div style='background:#f8d7da; color:#721c24; padding:15px; border-radius:8px; margin-bottom:20px;'><i class='fas fa-exclamation-triangle'></i> <?php echo $error; ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: <?php echo ($id > 0) ? '1fr 1fr' : '1fr'; ?>; gap: 30px;">
    
    <div class="card">
        <h3 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:10px; color: var(--klyp-dark);">Datos Fiscales y Acceso</h3>
        <form method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="save_cliente">
            
            <div style="display:grid; grid-template-columns: 2fr 1fr; gap:15px; margin-bottom: 15px;">
                <div class="form-group" style="margin-bottom:0;">
                    <label>Nombre Empresa / Razón Social: *</label>
                    <input type="text" name="nombre_empresa" value="<?php echo htmlspecialchars($cliente['nombre_empresa'] ?? ''); ?>" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>CIF / NIF: *</label>
                    <input type="text" name="cif" value="<?php echo htmlspecialchars($cliente['cif'] ?? ''); ?>" required>
                </div>
            </div>

            <div style="display:grid; grid-template-columns: 2fr 1fr; gap:15px; margin-bottom: 25px;">
                <div class="form-group" style="margin-bottom:0;">
                    <label>Email (Usuario de Acceso): *</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($cliente['email'] ?? ''); ?>" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>Contraseña Acceso: <?php echo ($id == 0) ? '*' : ''; ?></label>
                    <input type="password" name="password" placeholder="<?php echo ($id > 0) ? 'Dejar en blanco para no cambiar' : 'Asigna una contraseña'; ?>" <?php echo ($id == 0) ? 'required' : ''; ?> autocomplete="new-password">
                </div>
            </div>

            <h4 style="color: var(--text-gray); margin-bottom: 10px;">Dirección de Facturación</h4>
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Dirección completa:</label>
                <input type="text" name="direccion" value="<?php echo htmlspecialchars($cliente['direccion'] ?? ''); ?>">
            </div>
            
            <div style="display:grid; grid-template-columns: 1fr 2fr 2fr; gap:15px; margin-bottom: 25px;">
                <div class="form-group" style="margin-bottom:0;">
                    <label>C.P.:</label>
                    <input type="text" name="cp" value="<?php echo htmlspecialchars($cliente['cp'] ?? ''); ?>">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>Población:</label>
                    <input type="text" name="poblacion" value="<?php echo htmlspecialchars($cliente['poblacion'] ?? ''); ?>">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>Provincia:</label>
                    <input type="text" name="provincia" value="<?php echo htmlspecialchars($cliente['provincia'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 25px; padding: 15px; background: #f9fbfd; border-radius: 8px; border: 1px dashed #cce7ff;">
                <label style="color: var(--klyp-blue); font-weight: bold;">Estado del Cliente:</label>
                <select name="estado" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    <?php 
                    $estados = ['En Pruebas', 'Activo', 'Inactivo', 'Moroso'];
                    $estado_actual = $cliente['estado'] ?? 'En Pruebas';
                    foreach ($estados as $est) {
                        $selected = ($est == $estado_actual) ? 'selected' : '';
                        echo "<option value=\"$est\" $selected>$est</option>";
                    }
                    ?>
                </select>
                <small style="color: var(--text-gray); display: block; margin-top: 5px;">Recuerda: Poner a un cliente en "Inactivo" es el método correcto para darle de baja sin perder sus datos.</small>
            </div>

            <div style="text-align: right;">
                <button type="submit" class="btn" style="background-color: var(--klyp-dark); color: white; padding: 12px 30px; font-size: 1.1rem;">
                    <i class="fas fa-save"></i> <?php echo ($id > 0) ? 'Guardar Cambios' : 'Dar de Alta Cliente'; ?>
                </button>
            </div>
        </form>
    </div>

    <?php if ($id > 0): ?>
    <div style="display: flex; flex-direction: column; gap: 30px;">
        
        <div class="card">
            <h3 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:10px; color: var(--klyp-blue);"><i class="fas fa-address-book"></i> Agenda de Contactos</h3>
            
            <form method="POST" style="display: flex; gap: 10px; margin-bottom: 20px;">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="add_contacto">
                <input type="text" name="nombre_contacto" placeholder="Ej: Juan (Gerencia)" required style="flex: 2; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                <input type="text" name="telefono" placeholder="Teléfono" required style="flex: 1.5; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                <button type="submit" class="btn" style="padding: 8px 15px;"><i class="fas fa-plus"></i></button>
            </form>

            <?php if(count($contactos) > 0): ?>
                <table class="data-table" style="font-size: 0.9rem;">
                    <tbody>
                        <?php foreach($contactos as $con): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($con['nombre_contacto']); ?></strong></td>
                            <td><?php echo htmlspecialchars($con['telefono']); ?></td>
                            <td style="text-align: right;">
                                <form method="POST" style="display:inline;" onsubmit="return confirm('¿Borrar este teléfono?')">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="del_contacto" value="<?php echo $con['id']; ?>">
                                    <button type="submit" style="color:#dc3545; background:none; border:1px solid #dc3545; padding:3px 8px; border-radius:4px; cursor:pointer;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: #888; text-align: center; font-size: 0.9rem;">Aún no hay teléfonos registrados.</p>
            <?php endif; ?>
        </div>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom:1px solid #eee; padding-bottom:10px; margin-bottom: 15px;">
                <h3 style="margin:0; color: #28a745;"><i class="fas fa-boxes"></i> Suscripciones a App's</h3>
                <a href="#" class="btn" style="background-color: #28a745; color: white; padding: 5px 15px; font-size: 0.85rem; border-radius: 4px;">+ Añadir App</a>
            </div>

            <?php if(count($suscripciones) > 0): ?>
                <?php foreach($suscripciones as $sub): ?>
                    <div style="display: flex; align-items: center; padding: 15px; border: 1px solid #f0f0f0; border-radius: 8px; margin-bottom: 10px; background: #fafbfc;">
                        <div style="font-size: 2rem; color: var(--klyp-blue); margin-right: 15px;">
                            <i class="fas <?php echo htmlspecialchars($sub['icono']); ?>"></i>
                        </div>
                        <div style="flex: 1;">
                            <h4 style="margin: 0 0 5px 0; font-size: 1rem; color: var(--klyp-dark);"><?php echo htmlspecialchars($sub['titulo']); ?></h4>
                            <div style="font-size: 0.85rem; color: var(--text-gray);">
                                <strong>Estado:</strong> <?php echo htmlspecialchars($sub['estado']); ?> | 
                                <strong>Renovación:</strong> <?php echo date('d/m/Y', strtotime($sub['fecha_renovacion'])); ?> | 
                                <strong>Cuota:</strong> <?php echo number_format($sub['precio'], 2, ',', '.'); ?>€ (<?php echo htmlspecialchars($sub['ciclo']); ?>)
                            </div>
                        </div>
                        <div>
                            <a href="#" style="color: var(--klyp-blue); text-decoration: none; font-size: 0.9rem;"><i class="fas fa-cog"></i> Gestionar</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 20px; color: #888; font-size: 0.95rem;">
                    <i class="fas fa-box-open" style="font-size: 2rem; color: #ddd; margin-bottom: 10px; display: block;"></i>
                    Este cliente aún no tiene ninguna suscripción activa a nuestras aplicaciones.
                </div>
            <?php endif; ?>
        </div>

    </div>
    <?php endif; ?>

</div>

</div></body></html>