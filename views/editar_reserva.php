<?php
require_once('../includes/header.php');
require_once('../includes/conexion.php');

if (!isset($_SESSION['user']) || !isset($_GET['id'])) {
    header("Location: reservas.php");
    exit();
}

$id_reserva = (int)$_GET['id'];
$reserva = null;

// Obtener datos actuales
try {
    $stmt = $conn->prepare("SELECT * FROM reservas WHERE id_reserva = ?");
    $stmt->execute([$id_reserva]);
    $reserva = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

if (!$reserva) {
    header("Location: reservas.php?error=no_encontrado");
    exit();
}

// Valores por defecto (o del formulario si se recarga)
$fecha = $_GET['fecha'] ?? $reserva['fecha'];
$hora = $_GET['hora'] ?? date('H:i', strtotime($reserva['hora_inicio']));
$personas = $_GET['personas'] ?? $reserva['personas'];
$nombre_cliente = $_GET['nombre_cliente'] ?? $reserva['nombre_cliente'];
$id_recurso_actual = $reserva['id_recurso'];

$mesas_disponibles = [];
$busqueda_realizada = false;

if (isset($_GET['check'])) {
    $busqueda_realizada = true;
    try {
        $hora_inicio = $hora;
        $hora_final = date('H:i', strtotime($hora . ' + 1 hour'));

        // Buscar mesas disponibles (excluyendo la propia reserva actual del conflicto)
        $sql = "
            SELECT r.*, s.nombre as nombre_sala 
            FROM recursos r
            LEFT JOIN recursos_jerarquia rj ON r.id_recurso = rj.id_recurso_hijo
            LEFT JOIN recursos s ON rj.id_recurso_padre = s.id_recurso
            WHERE r.tipo = 'mesa' 
            AND r.capacidad >= ?
            AND r.id_recurso NOT IN (
                SELECT id_recurso FROM reservas 
                WHERE fecha = ? 
                AND id_reserva != ?  -- Excluir esta reserva
                AND (
                    (hora_inicio <= ? AND hora_final > ?) OR
                    (hora_inicio < ? AND hora_final >= ?) OR
                    (hora_inicio >= ? AND hora_final <= ?)
                )
            )
            ORDER BY r.capacidad ASC, s.nombre ASC
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$personas, $fecha, $id_reserva, $hora_inicio, $hora_inicio, $hora_final, $hora_final, $hora_inicio, $hora_final]);
        $mesas_disponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        $error = "Error al buscar mesas: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Reserva - Demon Deburgers</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="body-restaurante">
    <div class="trabajadores-container">
        <aside class="trabajadores-sidebar">
            <h1 class="trabajadores-title">Editar Reserva</h1>
            <a href="reservas.php" class="btn-new-user">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </aside>

        <section class="trabajadores-list-section">
            <h2 class="trabajadores-subtitle">Modificar Datos</h2>
            
            <form action="" method="GET" class="form-reserva" id="formBuscar">
                <input type="hidden" name="id" value="<?= $id_reserva ?>">
                <div class="form-group">
                    <label>Nombre Cliente:</label>
                    <input type="text" name="nombre_cliente" value="<?= htmlspecialchars($nombre_cliente) ?>" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Fecha:</label>
                    <input type="date" name="fecha" id="fechaReserva" value="<?= htmlspecialchars($fecha) ?>" min="<?= date('Y-m-d') ?>" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Hora:</label>
                    <input type="time" name="hora" value="<?= htmlspecialchars($hora) ?>" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Personas:</label>
                    <input type="number" name="personas" value="<?= htmlspecialchars($personas) ?>" min="1" required class="form-control">
                </div>
                <button type="submit" name="check" value="1" class="btn-action btn-edit" style="width:100%; margin-top:10px;">
                    <i class="fas fa-search"></i> Verificar Disponibilidad
                </button>
            </form>

            <?php if ($busqueda_realizada): ?>
                <h3 style="margin-top: 20px; color: #d4af37;">Mesas Disponibles</h3>
                <?php if (empty($mesas_disponibles)): ?>
                    <p class="no-data">No hay mesas disponibles para esa capacidad y horario.</p>
                <?php else: ?>
                    <form action="../proc/proc_editar_reserva.php" method="POST">
                        <input type="hidden" name="id_reserva" value="<?= $id_reserva ?>">
                        <input type="hidden" name="nombre_cliente" value="<?= htmlspecialchars($nombre_cliente) ?>">
                        <input type="hidden" name="fecha" value="<?= htmlspecialchars($fecha) ?>">
                        <input type="hidden" name="hora" value="<?= htmlspecialchars($hora) ?>">
                        <input type="hidden" name="personas" value="<?= htmlspecialchars($personas) ?>">
                        
                        <div class="mesas-grid" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px;">
                            <?php foreach ($mesas_disponibles as $mesa): ?>
                                <?php 
                                    $checked = ($mesa['id_recurso'] == $id_recurso_actual) ? 'checked' : ''; 
                                    $style = ($mesa['id_recurso'] == $id_recurso_actual) ? 'border-color: #d4af37; background: rgba(212, 175, 55, 0.2);' : 'border: 1px solid #444; background: rgba(0,0,0,0.5);';
                                ?>
                                <label class="mesa-option" style="<?= $style ?> padding: 10px; border-radius: 5px; cursor: pointer;">
                                    <input type="radio" name="id_recurso" value="<?= $mesa['id_recurso'] ?>" <?= $checked ?> required>
                                    <strong><?= htmlspecialchars($mesa['nombre']) ?></strong><br>
                                    <small><?= htmlspecialchars($mesa['nombre_sala']) ?></small><br>
                                    <span style="color: #2ecc71;"><?= $mesa['capacidad'] ?> pax</span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        
                        <button type="submit" class="btn-action btn-edit" style="width:100%; margin-top:20px; background-color: #2ecc71;">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </form>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-info" style="margin-top: 20px;">
                    Dale a "Verificar Disponibilidad" para confirmar la mesa o cambiarla.
                </div>
            <?php endif; ?>
        </section>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/validacion_reserva.js"></script>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
