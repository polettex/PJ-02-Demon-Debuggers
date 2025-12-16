<?php
require_once('../includes/header.php');
require_once('../includes/conexion.php');

// Verificar sesión
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

// Obtener reservas futuras (o todas)
// Unimos con recursos para saber qué mesa es, y con usuarios para saber qué camarero la hizo
try {
    $stmt = $conn->prepare("
        SELECT r.id_reserva, r.fecha, r.hora_inicio, r.hora_final, r.nombre_cliente, r.personas,
               rec.nombre as nombre_mesa,
               u.nombre as nombre_camarero
        FROM reservas r
        INNER JOIN recursos rec ON r.id_recurso = rec.id_recurso
        INNER JOIN usuarios u ON r.id_usuario = u.id_usuario
        WHERE r.nombre_cliente != 'Cliente Casual'
        ORDER BY r.fecha ASC, r.hora_inicio ASC
    ");
    $stmt->execute();
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $reservas = [];
    $error = "Error al cargar reservas: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas - Demon Deburgers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="body-restaurante">
    <div class="trabajadores-container"> <!-- Reusamos contenedor de trabajadores para estilo similar -->
        
        <aside class="trabajadores-sidebar">
            <h1 class="trabajadores-title">Reservas</h1>
            <a href="crear_reserva.php" class="btn-new-user">
                <i class="fas fa-plus-circle"></i> Nueva Reserva
            </a>
            <div class="sidebar-info">
                <p style="color: #d4af37; margin-top: 2rem; font-size: 0.9rem;">
                    <i class="fas fa-info-circle"></i> Gestiona las reservas anticipadas.
                </p>
            </div>
        </aside>

        <section class="trabajadores-list-section">
            <h2 class="trabajadores-subtitle">Próximas Reservas</h2>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <?php 
                    if ($_GET['success'] == 'creado') echo "Reserva creada correctamente.";
                    if ($_GET['success'] == 'editado') echo "Reserva actualizada correctamente.";
                    if ($_GET['success'] == 'eliminado') echo "Reserva eliminada correctamente.";
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <?php 
                    if ($_GET['error'] == 'ocupado') echo "La mesa seleccionada ya está ocupada en ese horario.";
                    if ($_GET['error'] == 'db_error') echo "Error en la base de datos.";
                    if ($_GET['error'] == 'campos_vacios') echo "Todos los campos son obligatorios.";
                    if ($_GET['error'] == 'fecha_invalida') echo "No se puede reservar en una fecha pasada.";
                    ?>
                </div>
            <?php endif; ?>

            <div class="trabajadores-table-wrapper">
                <table class="trabajadores-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Cliente</th>
                            <th>Pax</th>
                            <th>Mesa</th>
                            <th>Camarero</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservas as $res): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($res['fecha'])) ?></td>
                            <td><?= date('H:i', strtotime($res['hora_inicio'])) ?> - <?= $res['hora_final'] ? date('H:i', strtotime($res['hora_final'])) : '?' ?></td>
                            <td><?= htmlspecialchars($res['nombre_cliente']) ?></td>
                            <td><?= (int)$res['personas'] ?></td>
                            <td><?= htmlspecialchars($res['nombre_mesa']) ?></td>
                            <td><?= htmlspecialchars($res['nombre_camarero']) ?></td>
                            <td class="actions-cell">
                                <a href="editar_reserva.php?id=<?= $res['id_reserva'] ?>" class="btn-action btn-edit" title="Editar"><i class="fas fa-edit"></i></a>
                                <button onclick="confirmarEliminar(<?= $res['id_reserva'] ?>, '<?= htmlspecialchars($res['nombre_cliente']) ?>')" class="btn-action btn-delete" title="Eliminar"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($reservas)): ?>
                        <tr><td colspan="7" style="text-align:center;">No hay reservas registradas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <!-- Formulario oculto para eliminar -->
    <form id="formEliminar" action="../proc/proc_eliminar_reserva.php" method="POST" style="display:none;">
        <input type="hidden" name="id_reserva" id="id_reserva_eliminar">
    </form>

    <script>
    function confirmarEliminar(id, nombre) {
        Swal.fire({
            title: '¿Estás seguro?',
            html: `¿Deseas eliminar la reserva de <strong>${nombre}</strong>?<br>Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#95a5a6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('id_reserva_eliminar').value = id;
                document.getElementById('formEliminar').submit();
            }
        })
    }
    </script>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
