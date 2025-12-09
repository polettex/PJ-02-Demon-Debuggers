<?php
// Incluimos cabecera y conexión a la base de datos
require_once('../includes/header.php'); 
require_once('../includes/conexion.php');

// Obtenemos el ID de la sala desde la URL (GET). Si no existe o es inválido, redirigimos.
$salaId = isset($_GET['sala']) ? (int)$_GET['sala'] : 0;
if ($salaId <= 0) { 
    header('Location: restaurante.php'); 
    exit; 
}

/* -------- CARGAR SALA -------- */
try {
    // Preparamos consulta para obtener datos de la sala desde recursos
    $stmtSala = $conn->prepare('SELECT id_recurso, nombre, capacidad FROM recursos WHERE id_recurso = ? AND tipo = "sala"');
    $stmtSala->execute([$salaId]);
    $sala = $stmtSala->fetch(PDO::FETCH_ASSOC);

    // Si no existe la sala, redirigimos
    if (!$sala) { 
        header('Location: restaurante.php'); 
        exit; 
    }
} catch (Exception $e) {
    // En caso de error en la consulta, redirigimos
    header('Location: restaurante.php'); 
    exit;
}

/* -------- CARGAR MESAS -------- */
try {
    // Obtenemos todas las mesas de la sala usando la tabla de jerarquía
    $stmtMesas = $conn->prepare('
        SELECT r.id_recurso as id_mesa, r.capacidad, r.estado 
        FROM recursos r
        INNER JOIN recursos_jerarquia rh ON r.id_recurso = rh.id_recurso_hijo
        WHERE rh.id_recurso_padre = ? 
        AND r.tipo = "mesa"
        ORDER BY r.id_recurso ASC
    ');
    $stmtMesas->execute([$salaId]);
    $mesas = $stmtMesas->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Si falla la consulta, dejamos el array vacío
    $mesas = [];
}

/* -------- CONTADORES -------- */
// Contamos mesas totales y calculamos ocupadas/libres
$total = count($mesas);
$ocupadas = 0;
foreach ($mesas as $m) 
    if ($m['estado'] === 'ocupado') $ocupadas++;
$libres = $total - $ocupadas;

/* -------- FONDO -------- */
// Usamos fondo de piedra para todas las salas
$bg = '../img/fondo_piedra.jpg';

/* -------- LAYOUT -------- */
// Definimos la clase CSS del layout según el tipo de sala
$nombre = strtolower($sala['nombre']);
if (in_array($nombre, ['patio 1','patio 2','patio 3'], true))
    $layoutClass = 'layout--patio';
elseif (in_array($nombre, ['comedor 1','comedor 2'], true)) 
    $layoutClass = 'layout--comedor';
elseif (in_array($nombre, ['privado 1','privado 2'], true)) 
    $layoutClass = 'layout--priv12';
elseif (in_array($nombre, ['privado 3','privado 4'], true)) 
    $layoutClass = 'layout--priv34';
else 
    // Si no coincide, elegimos layout según número de mesas
    $layoutClass = ($total === 1) ? 'layout--priv12' : (($total === 2) ? 'layout--priv34' : 'layout--patio');
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($sala['nombre']) ?> — Demon Deburgers</title>
<link rel="stylesheet" href="../css/styles.css">
</head>
<body class="body-restaurante sala-page">
    <div class="sala-layout">

    <!-- LATERAL: información de la sala -->
    <aside class="sala-side">
        <!-- Título con nombre de la sala -->
        <h1 class="sala-title"><?= htmlspecialchars($sala['nombre']) ?></h1>

        <!-- Mensaje de bienvenida si hay sesión iniciada -->
        <?php if (!empty($_SESSION['nombre'])): ?>
            <div class="sala-welcome">Bienvenido <?= htmlspecialchars($_SESSION['nombre']) ?></div>
        <?php endif; ?>

        <!-- Estadísticas de la sala -->
        <div>
            <span class="sala-pill"><b>Mesas:</b> <?= (int)$total ?></span>
            <span class="sala-pill"><b>Libres:</b> <?= (int)$libres ?></span>
            <span class="sala-pill"><b>Ocupadas:</b> <?= (int)$ocupadas ?></span>
            <span class="sala-pill"><b>Capacidad total:</b> <?= (int)$sala['capacidad'] ?></span>
        </div>

        <!-- Tabla con listado de mesas -->
        <table class="sala-table">
            <thead>
                <tr><th>Mesa</th><th>Cap.</th><th>Estado</th></tr>
            </thead>
            <tbody>
            <?php foreach ($mesas as $m){ ?>
                <tr>
                    <td>#<?= (int)$m['id_mesa'] ?></td>
                    <td><?= (int)$m['capacidad'] ?> max</td>
                    <td>
                        <?php
                            // Definimos clase y texto según estado
                            if ($m['estado'] === 'ocupado') {
                                $clase = 'sala-badge--ocupada';
                                $texto = 'Ocupada';
                            } else {
                                $clase = 'sala-badge--libre';
                                $texto = 'Libre';
                            }
                        ?>
                        <span class="sala-badge <?= $clase ?>">
                            <?= $texto ?>
                        </span>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <!-- Botón para ocupar todas las mesas -->
        <form method="post" action="../proc/toggle_todas.php">
            <input type="hidden" name="sala" value="<?= (int)$salaId ?>">
            <button type="submit" class="btn-toggle-todas">Alternar todas las mesas</button>
        </form>

    </aside>

    <!-- TABLERO: representación gráfica de las mesas -->
    <section class="sala-board <?= $layoutClass ?>" style="background-image:url('<?= htmlspecialchars($bg) ?>')">
        <div class="sala-canvas">
            <!-- Columnas del tablero -->
            <span class="sala-col sala-col--1"></span>
            <span class="sala-col sala-col--2"></span>
            <span class="sala-col sala-col--3"></span>

            <!-- Renderizamos cada mesa como un formulario con div -->
            <?php foreach ($mesas as $m){
                $cap = (int)$m['capacidad'];
                $estadoClass = ($m['estado'] === 'ocupado') ? 'ocupado' : 'disponible';
            ?>
            <form method="post" action="../proc/toggle_mesa.php" class="sala-mesa" data-id="<?= (int)$m['id_mesa'] ?>">
                <!-- Campos ocultos para identificar sala y mesa -->
                <input type="hidden" name="sala" value="<?= (int)$salaId ?>">
                <input type="hidden" name="toggle" value="<?= (int)$m['id_mesa'] ?>">
                <!-- Div con información de la mesa -->
                <button type="submit" class="sala-mesaBtn <?= $estadoClass ?>" title="Cambiar estado">
                    <div class="mesa-info">
                        <div class="mesa-numero">Mesa #<?= (int)$m['id_mesa'] ?></div>
                        <div class="mesa-sillas"><?= (int)$cap ?> sillas</div>
                    </div>
                </button>
            </form>
            <?php } ?>
        </div>
    </section>
    </div>

    <!-- Pie de página -->
    <?php include '../includes/footer.php'; ?>
</body>
</html>
