<?php
// Incluimos cabecera y conexión a la base de datos
require_once('../includes/header.php'); 
require_once('../includes/conexion.php');

// Verificar que el usuario sea administrador
if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 4) {
    header('Location: restaurante.php');
    exit;
}

/* -------- OBTENER TODOS LOS TRABAJADORES -------- */
try {
    $stmtTrabajadores = $conn->prepare('
        SELECT u.id_usuario, u.nombre, u.apellidos, u.user, r.nombre as rol_nombre, u.id_rol
        FROM usuarios u
        INNER JOIN roles r ON u.id_rol = r.id_rol
        ORDER BY u.id_usuario ASC
    ');
    $stmtTrabajadores->execute();
    $trabajadores = $stmtTrabajadores->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $trabajadores = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gestión de Trabajadores — Demon Deburgers</title>
<link rel="stylesheet" href="../css/styles.css">
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="body-restaurante trabajadores-page">
    <div class="trabajadores-container">
        
        <!-- COLUMNA IZQUIERDA: Botón de crear -->
        <aside class="trabajadores-sidebar">
            <h1 class="trabajadores-title">Gestión de Trabajadores</h1>
            
            <a href="register.php" class="btn-new-user">
                <i class="fas fa-user-plus"></i> Registrar Nuevo Usuario
            </a>
            
            <div class="sidebar-info">
                <p style="color: #d4af37; margin-top: 2rem; font-size: 0.9rem;">
                    <i class="fas fa-info-circle"></i> Administra los usuarios del sistema desde aquí.
                </p>
            </div>
        </aside>
        
        <!-- COLUMNA DERECHA: Tabla de trabajadores -->
        <section class="trabajadores-list-section">
            <h2 class="trabajadores-subtitle">Trabajadores Registrados</h2>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <?php
                        if ($_GET['success'] == 'creado') echo "✓ Trabajador creado exitosamente";
                        elseif ($_GET['success'] == 'actualizado') echo "✓ Trabajador actualizado exitosamente";
                        elseif ($_GET['success'] == 'eliminado') echo "✓ Trabajador eliminado exitosamente";
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <?php
                        if ($_GET['error'] == 'no_auto_editar') echo "✗ No puedes editarte a ti mismo";
                        elseif ($_GET['error'] == 'no_auto_eliminar') echo "✗ No puedes eliminarte a ti mismo";
                        elseif ($_GET['error'] == 'no_encontrado') echo "✗ Trabajador no encontrado";
                        else echo "✗ Error al procesar la solicitud";
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (count($trabajadores) > 0): ?>
                <div class="trabajadores-table-wrapper">
                    <table class="trabajadores-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellidos</th>
                                <th>Usuario</th>
                                <th>Rol</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($trabajadores as $t): ?>
                                <?php $esUsuarioActual = ($t['id_usuario'] == $_SESSION['id_camarero']); ?>
                                <tr>
                                    <td><?= (int)$t['id_usuario'] ?></td>
                                    <td><?= htmlspecialchars($t['nombre']) ?></td>
                                    <td><?= htmlspecialchars($t['apellidos']) ?></td>
                                    <td><?= htmlspecialchars($t['user']) ?></td>
                                    <td>
                                        <span class="rol-badge rol-<?= (int)$t['id_rol'] ?>">
                                            <?= htmlspecialchars($t['rol_nombre']) ?>
                                        </span>
                                    </td>
                                    <td class="actions-cell">
                                        <?php if ($esUsuarioActual): ?>
                                            <span class="badge-self">Tú</span>
                                        <?php else: ?>
                                            <a href="editar_trabajador.php?id=<?= (int)$t['id_usuario'] ?>" 
                                               class="btn-action btn-edit" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn-action btn-delete" 
                                                    title="Eliminar"
                                                    onclick="confirmarEliminacion(<?= (int)$t['id_usuario'] ?>, '<?= htmlspecialchars($t['nombre']) ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="no-data">No hay trabajadores registrados.</p>
            <?php endif; ?>
        </section>
        
    </div>
    
    <!-- Formulario oculto para eliminación -->
    <form id="formEliminar" method="post" action="../proc/proc_eliminar_trabajador.php" style="display:none;">
        <input type="hidden" name="id_usuario" id="idEliminar">
    </form>
    
    <script>
    function confirmarEliminacion(id, nombre) {
        Swal.fire({
            title: '¿Estás seguro?',
            html: `¿Deseas eliminar al usuario <strong>${nombre}</strong>?<br>Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#95a5a6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('idEliminar').value = id;
                document.getElementById('formEliminar').submit();
            }
        });
    }
    </script>
    
    <!-- Pie de página -->
    <?php include '../includes/footer.php'; ?>
</body>
</html>
