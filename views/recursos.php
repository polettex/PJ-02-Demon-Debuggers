<?php
// Incluimos cabecera y conexión a la base de datos
require_once('../includes/header.php'); 
require_once('../includes/conexion.php');

// Verificar que el usuario sea administrador
if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 4) {
    header('Location: restaurante.php');
    exit;
}

/* -------- OBTENER TODOS LOS RECURSOS -------- */
try {
    $stmtRecursos = $conn->prepare('
        SELECT id_recurso, tipo, nombre, capacidad, estado, imagen
        FROM recursos
        WHERE tipo IN ("sala", "mesa")
        ORDER BY 
            CASE tipo 
                WHEN "sala" THEN 1 
                WHEN "mesa" THEN 2 
            END,
            id_recurso ASC
    ');
    $stmtRecursos->execute();
    $recursos = $stmtRecursos->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $recursos = [];
}

// Separar recursos por tipo
$salas = array_filter($recursos, fn($r) => $r['tipo'] === 'sala');
$mesas = array_filter($recursos, fn($r) => $r['tipo'] === 'mesa');
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gestión de Recursos — Demon Deburgers</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="../css/styles.css">
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="body-restaurante trabajadores-page">
    <div class="trabajadores-container">
        
        <!-- COLUMNA IZQUIERDA: Botón de crear -->
        <aside class="trabajadores-sidebar">
            <h1 class="trabajadores-title">Gestión de Recursos</h1>
            
            <a href="crear_recurso.php" class="btn-new-user">
                <i class="fas fa-plus-circle"></i> Crear Nuevo Recurso
            </a>
            
            <div class="sidebar-info">
                <p style="color: #d4af37; margin-top: 2rem; font-size: 0.9rem;">
                    <i class="fas fa-info-circle"></i> Administra salas y mesas del restaurante.
                </p>
                <div style="margin-top: 1rem; font-size: 0.85rem; color: #ccc;">
                    <strong>Total recursos:</strong><br>
                    🏠 Salas: <?= count($salas) ?><br>
                    🪑 Mesas: <?= count($mesas) ?>
                </div>
            </div>
        </aside>
        
        <!-- COLUMNA DERECHA: Tabla de recursos -->
        <section class="trabajadores-list-section">
            <h2 class="trabajadores-subtitle">Recursos Registrados</h2>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <?php
                        if ($_GET['success'] == 'creado') echo "✓ Recurso creado exitosamente";
                        elseif ($_GET['success'] == 'actualizado') echo "✓ Recurso actualizado exitosamente";
                        elseif ($_GET['success'] == 'eliminado') echo "✓ Recurso eliminado exitosamente";
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <?php
                        if ($_GET['error'] == 'no_encontrado') echo "✗ Recurso no encontrado";
                        elseif ($_GET['error'] == 'tiene_relaciones') echo "✗ No se puede eliminar: el recurso tiene relaciones";
                        else echo "✗ Error al procesar la solicitud";
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (count($recursos) > 0): ?>
                <div class="trabajadores-table-wrapper">
                    <table class="trabajadores-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tipo</th>
                                <th>Nombre</th>
                                <th>Capacidad</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recursos as $r): ?>
                                <tr>
                                    <td><?= (int)$r['id_recurso'] ?></td>
                                    <td>
                                        <span class="tipo-badge tipo-<?= htmlspecialchars($r['tipo']) ?>">
                                            <?php
                                                $iconos = ['sala' => '🏠', 'mesa' => '🪑'];
                                                echo $iconos[$r['tipo']] . ' ' . ucfirst($r['tipo']);
                                            ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($r['nombre']) ?></td>
                                    <td><?= $r['capacidad'] ? (int)$r['capacidad'] : '-' ?></td>
                                    <td>
                                        <?php if ($r['estado']): ?>
                                            <span class="estado-badge estado-<?= htmlspecialchars($r['estado']) ?>">
                                                <?= ucfirst($r['estado']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color: #999;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="actions-cell">
                                        <a href="editar_recurso.php?id=<?= (int)$r['id_recurso'] ?>" 
                                           class="btn-action btn-edit" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn-action btn-delete" 
                                                title="Eliminar"
                                                onclick="confirmarEliminacion(<?= (int)$r['id_recurso'] ?>, '<?= htmlspecialchars($r['nombre']) ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="no-data">No hay recursos registrados.</p>
            <?php endif; ?>
        </section>
        
    </div>
    
    <!-- Formulario oculto para eliminación -->
    <form id="formEliminar" method="post" action="../proc/proc_eliminar_recurso.php" style="display:none;">
        <input type="hidden" name="id_recurso" id="idEliminar">
    </form>
    
    <script>
    function confirmarEliminacion(id, nombre) {
        Swal.fire({
            title: '¿Estás seguro?',
            html: `¿Deseas eliminar el recurso <strong>${nombre}</strong>?<br>Esta acción no se puede deshacer.`,
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
