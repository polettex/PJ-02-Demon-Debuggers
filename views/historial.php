<?php
// Conexión a la base de datos
include '../includes/conexion.php';

/* ============================
   Preparar selects dependientes
   ============================ */
// Variables que recogen los filtros desde la URL (GET)
$salaElegida     = $_GET['sala']     ?? '';
$mesaElegida     = $_GET['mesa']     ?? '';
$fechaElegida    = $_GET['fecha']    ?? '';
$camareroElegido = $_GET['camarero'] ?? '';

// 1) Buscar id_recurso de sala si hay sala elegida
$salaId = null;
if ($salaElegida !== '') {
    // Consulta para obtener el id_recurso a partir del nombre
    $stmtSala = $conn->prepare("SELECT id_recurso FROM recursos WHERE nombre = ? AND tipo = 'sala' LIMIT 1");
    $stmtSala->execute([$salaElegida]);
    $rowSala = $stmtSala->fetch(PDO::FETCH_ASSOC);
    if ($rowSala) $salaId = (int)$rowSala['id_recurso'];
}

// 2) Cargar mesas para el select según sala
if ($salaId) {
    // Si hay sala seleccionada, cargamos solo sus mesas usando jerarquía
    $stmtMesasSel = $conn->prepare("
        SELECT r.id_recurso 
        FROM recursos r
        INNER JOIN recursos_jerarquia rh ON r.id_recurso = rh.id_recurso_hijo
        WHERE rh.id_recurso_padre = ? 
        AND r.tipo = 'mesa'
        ORDER BY r.id_recurso ASC
    ");
    $stmtMesasSel->execute([$salaId]);
} else {
    // Si no hay sala seleccionada aún, mostramos todas las mesas
    $stmtMesasSel = $conn->query("SELECT id_recurso FROM recursos WHERE tipo = 'mesa' ORDER BY id_recurso ASC");
}
// Guardamos los IDs de mesas en un array para el <select>
$mesasParaSelect = $stmtMesasSel->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de ocupaciones</title>
    <!-- Iconos y estilos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body class="login-body">
    <div class="historial-tori">
        <div class="historial-div">
            <h1 class="historial-titulo">Registros</h1>

            <!-- Barra de filtros -->
            <div class="filterbar">
                <form method="GET" action="" class="filtro-form">
                    <!-- Select de salas -->
                    <label for="sala">Sala:</label>
                    <select name="sala" id="sala">
                        <option value="">-- Todas --</option>
                        <!-- Opciones fijas de salas, con "selected" si coincide con el filtro -->
                        <option value="Patio 1"   <?= ($salaElegida==='Patio 1'   ? 'selected' : '') ?>>Patio 1</option>
                        <option value="Patio 2"   <?= ($salaElegida==='Patio 2'   ? 'selected' : '') ?>>Patio 2</option>
                        <option value="Patio 3"   <?= ($salaElegida==='Patio 3'   ? 'selected' : '') ?>>Patio 3</option>
                        <option value="Comedor 1" <?= ($salaElegida==='Comedor 1' ? 'selected' : '') ?>>Comedor 1</option>
                        <option value="Comedor 2" <?= ($salaElegida==='Comedor 2' ? 'selected' : '') ?>>Comedor 2</option>
                        <option value="Privado 1" <?= ($salaElegida==='Privado 1' ? 'selected' : '') ?>>Privado 1</option>
                        <option value="Privado 2" <?= ($salaElegida==='Privado 2' ? 'selected' : '') ?>>Privado 2</option>
                        <option value="Privado 3" <?= ($salaElegida==='Privado 3' ? 'selected' : '') ?>>Privado 3</option>
                        <option value="Privado 4" <?= ($salaElegida==='Privado 4' ? 'selected' : '') ?>>Privado 4</option>
                    </select>

                    <!-- Select de mesas dinámico -->
                    <label for="mesa">Mesa:</label>
                    <select name="mesa" id="mesa">
                        <option value="">-- Todas --</option>
                        <?php foreach ($mesasParaSelect as $idMesaOpt): ?>
                            <option value="<?= (int)$idMesaOpt ?>"
                                <?= ($mesaElegida !== '' && (int)$mesaElegida === (int)$idMesaOpt) ? 'selected' : '' ?>>
                                Mesa <?= (int)$idMesaOpt ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Filtro por fecha -->
                    <label for="fecha">Fecha:</label>
                    <input type="date" name="fecha" id="fecha" value="<?= htmlspecialchars($fechaElegida) ?>">

                    <!-- Filtro por camarero -->
                    <label for="camarero">Camarero/a:</label>
                    <select name="camarero" id="camarero">
                        <option value="">-- Todos --</option>
                        <!-- Lista fija de camareros -->
                        <option value="Luis"   <?= ($camareroElegido==='Luis'   ? 'selected' : '') ?>>Luis</option>
                        <option value="María"  <?= ($camareroElegido==='María'  ? 'selected' : '') ?>>María</option>
                        <option value="Carlos" <?= ($camareroElegido==='Carlos' ? 'selected' : '') ?>>Carlos</option>
                        <option value="Ana"    <?= ($camareroElegido==='Ana'    ? 'selected' : '') ?>>Ana</option>
                        <option value="Jorge"  <?= ($camareroElegido==='Jorge'  ? 'selected' : '') ?>>Jorge</option>
                        <option value="Lucía"  <?= ($camareroElegido==='Lucía'  ? 'selected' : '') ?>>Lucía</option>
                        <option value="Miguel" <?= ($camareroElegido==='Miguel' ? 'selected' : '') ?>>Miguel</option>
                        <option value="Sara"   <?= ($camareroElegido==='Sara'   ? 'selected' : '') ?>>Sara</option>
                        <option value="Diego"  <?= ($camareroElegido==='Diego'  ? 'selected' : '') ?>>Diego</option>
                        <option value="Elena"  <?= ($camareroElegido==='Elena'  ? 'selected' : '') ?>>Elena</option>
                    </select>

                    <!-- Botones de aplicar/borrar filtros -->
                    <div class="filter-buttons">
                        <button type="submit" class="buttonfilterbar">Aplicar filtros</button>
                        <button type="button" id="btnReset" class="buttonfilterbar">Borrar filtros</button>
                    </div>
                </form>
            </div>

            <!-- Contenedor de resultados -->
            <div class="historial-container">
                <?php
                // Recogemos filtros otra vez (se podría reutilizar las variables iniciales)
                $sala     = $_GET['sala']     ?? '';
                $mesa     = $_GET['mesa']     ?? '';
                $fecha    = $_GET['fecha']    ?? '';
                $camarero = $_GET['camarero'] ?? '';

                // Consulta base con JOINs para obtener reservas
                $sql = "SELECT 
                            sala.nombre AS Sala,
                            mesa.id_recurso AS IdMesa,
                            DATE_FORMAT(res.fecha, '%d/%m/%Y') AS Fecha,
                            DATE_FORMAT(res.hora_inicio, '%H:%i') AS HoraInicio,
                            DATE_FORMAT(res.hora_final,  '%H:%i') AS HoraFin,
                            CONCAT(u.nombre, ' ', u.apellidos) AS Camarero
                        FROM reservas res
                        INNER JOIN recursos mesa ON res.id_recurso = mesa.id_recurso
                        INNER JOIN recursos_jerarquia rh ON mesa.id_recurso = rh.id_recurso_hijo
                        INNER JOIN recursos sala ON rh.id_recurso_padre = sala.id_recurso
                        INNER JOIN usuarios u ON res.id_usuario = u.id_usuario
                        WHERE mesa.tipo = 'mesa' AND sala.tipo = 'sala'"; // condición base

                $params = [];

                // Filtros dinámicos según lo que haya seleccionado el usuario
                if ($sala !== '') {
                    $sql .= " AND sala.nombre LIKE ?";
                    $params[] = "%$sala%";
                }
                if ($mesa !== '') {
                    $sql .= " AND mesa.id_recurso = ?";
                    $params[] = (int)$mesa;
                }
                if ($fecha !== '') {
                    $sql .= " AND res.fecha = ?";
                    $params[] = $fecha;
                }
                if ($camarero !== '') {
                    // Buscamos por nombre, apellidos o nombre completo del usuario
                    $sql .= " AND (u.nombre LIKE ? 
                               OR u.apellidos LIKE ? 
                               OR CONCAT(TRIM(u.nombre),' ',TRIM(u.apellidos)) LIKE ?)";
                    $params[] = "%$camarero%";
                    $params[] = "%$camarero%";
                    $params[] = "%$camarero%";
                }

                // Ordenamos resultados por fecha y hora de inicio, descendente (últimos primero)
                $sql .= " ORDER BY res.fecha DESC, res.hora_inicio DESC";

                // Preparamos y ejecutamos la consulta con los parámetros
                $stmt = $conn->prepare($sql);
                $stmt->execute($params);
                $ocupaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Mostramos resultados
                if (count($ocupaciones) > 0) {
                    foreach ($ocupaciones as $fila) {
                        // Si no hay hora de fin, la mesa sigue ocupada
                        $estado = is_null($fila['HoraFin']) ? 'ocupada' : 'libre';

                        echo "<div class='historial-content' data-estado='{$estado}'>";
                        echo "<div class='dato'><strong>Lugar:</strong> {$fila['Sala']}</div>";
                        echo "<div class='dato'><strong>Mesa:</strong> {$fila['IdMesa']}</div>";
                        echo "<div class='dato'><strong>Fecha:</strong> {$fila['Fecha']}</div>";
                        echo "<div class='dato'><strong>Inicio:</strong> {$fila['HoraInicio']}</div>";
                        echo "<div class='dato'><strong>Camarero/a:</strong> {$fila['Camarero']}</div>";

                        // Mostramos fin si existe, si no indicamos que sigue ocupada
                        if (!is_null($fila['HoraFin'])) {
                            echo "<div class='dato'><strong>Fin:</strong> {$fila['HoraFin']}</div>";
                        } else {
                            echo "<div class='dato'><strong>Ocupada</strong></div>";
                        }
                        echo "</div>";
                    }
                } else {
                    // Mensaje si no hay registros
                    echo "No hay registros de ocupaciones.";
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Pie de página -->
    <?php include_once '../includes/footer.php'; ?>

    <!-- JavaScript externo para manejar filtros (ej: reset) -->
    <script src="../proc/proc_historial.js"></script>
</body>
</html>
