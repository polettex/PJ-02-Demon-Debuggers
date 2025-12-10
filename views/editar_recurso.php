<?php
// Verificar que el usuario sea administrador
require_once('../includes/header.php');
require_once('../includes/conexion.php');

if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 4) {
    header('Location: restaurante.php');
    exit;
}

// Obtener ID del recurso a editar
$idEditar = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($idEditar <= 0) {
    header('Location: recursos.php');
    exit;
}

// Obtener datos del recurso
try {
    $stmtRecurso = $conn->prepare('SELECT * FROM recursos WHERE id_recurso = ?');
    $stmtRecurso->execute([$idEditar]);
    $recurso = $stmtRecurso->fetch(PDO::FETCH_ASSOC);
    
    if (!$recurso) {
        header('Location: recursos.php?error=no_encontrado');
        exit;
    }
    
    // Si es una mesa, obtener la sala asignada
    $salaAsignada = null;
    if ($recurso['tipo'] === 'mesa') {
        $stmtSala = $conn->prepare('
            SELECT id_recurso_padre 
            FROM recursos_jerarquia 
            WHERE id_recurso_hijo = ?
        ');
        $stmtSala->execute([$idEditar]);
        $resultSala = $stmtSala->fetch(PDO::FETCH_ASSOC);
        $salaAsignada = $resultSala ? (int)$resultSala['id_recurso_padre'] : null;
    }
} catch (Exception $e) {
    header('Location: recursos.php?error=db_error');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Recurso</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="login-body">

    <div class="logo-container-left">
        <div class="div-logo">
            <img class="logo" src="../img/logo_sin_fondo" alt="logo">
        </div>
    </div>

    <div class="form-container-right">

    <div id="loginFormContainer">
        <form id="editForm" action="../proc/proc_editar_recurso.php" method="post" enctype="multipart/form-data" class="login-form" novalidate>
            <h2 class="form-title">Editar Recurso</h2>

            <input type="hidden" name="id_recurso" value="<?= (int)$recurso['id_recurso'] ?>">

            <div class="input-field">
                <span class="input-icon"><i class="fas fa-tag"></i></span>
                <select class="input" id="tipo" name="tipo" required onchange="toggleImageField()">
                    <option value="">Seleccionar tipo...</option>
                    <option value="sala" <?= $recurso['tipo'] == 'sala' ? 'selected' : '' ?>>🏠 Sala</option>
                    <option value="mesa" <?= $recurso['tipo'] == 'mesa' ? 'selected' : '' ?>>🪑 Mesa</option>
                </select>
                <div id="tipoError" class="input-error-message 
                    <?php if (isset($_GET['tipoVacio'])) echo 'active'; ?>">
                    <?php if (isset($_GET['tipoVacio'])) echo "Debe seleccionar un tipo"; ?>
                </div>
            </div>

            <div class="input-field">
                <span class="input-icon"><i class="fas fa-signature"></i></span>
                <input class="input" type="text" id="nombre" name="nombre" placeholder="Nombre" 
                       value="<?= htmlspecialchars($recurso['nombre']) ?>" required>
                <div id="nombreError" class="input-error-message 
                    <?php if (isset($_GET['nombreVacio'])) echo 'active'; ?>">
                    <?php if (isset($_GET['nombreVacio'])) echo "El nombre no puede estar vacío"; ?>
                </div>
            </div>

            <div class="input-field">
                <span class="input-icon"><i class="fas fa-users"></i></span>
                <input class="input" type="number" id="capacidad" name="capacidad" 
                       placeholder="Capacidad (opcional)" min="0"
                       value="<?= $recurso['capacidad'] ? (int)$recurso['capacidad'] : '' ?>">
                <small style="color: #666; font-size: 0.85rem; display: block; margin-top: 5px;">
                    Número de personas o elementos que puede contener
                </small>
            </div>

            <?php if ($recurso['tipo'] === 'mesa'): ?>
            <div class="input-field" id="salaField">
                <span class="input-icon"><i class="fas fa-door-open"></i></span>
                <select class="input" id="id_sala" name="id_sala">
                    <option value="">Sin asignar a sala</option>
                    <?php
                    try {
                        $stmtSalas = $conn->prepare('SELECT id_recurso, nombre FROM recursos WHERE tipo = "sala" ORDER BY nombre ASC');
                        $stmtSalas->execute();
                        $salasDisponibles = $stmtSalas->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($salasDisponibles as $sala) {
                            $selected = ($salaAsignada == $sala['id_recurso']) ? 'selected' : '';
                            echo '<option value="' . (int)$sala['id_recurso'] . '" ' . $selected . '>' . htmlspecialchars($sala['nombre']) . '</option>';
                        }
                    } catch (Exception $e) {
                        // Silencioso
                    }
                    ?>
                </select>
                <small style="color: #666; font-size: 0.85rem; display: block; margin-top: 5px;">
                    Asignar esta mesa a una sala específica
                </small>
            </div>
            <?php endif; ?>

            <div class="input-field">
                <span class="input-icon"><i class="fas fa-toggle-on"></i></span>
                <select class="input" id="estado" name="estado">
                    <option value="">Sin estado</option>
                    <option value="libre" <?= $recurso['estado'] == 'libre' ? 'selected' : '' ?>>Libre</option>
                    <option value="ocupado" <?= $recurso['estado'] == 'ocupado' ? 'selected' : '' ?>>Ocupado</option>
                </select>
                <small style="color: #666; font-size: 0.85rem; display: block; margin-top: 5px;">
                    Opcional - Aplica principalmente a mesas y salas
                </small>
            </div>

            <?php if ($recurso['tipo'] === 'sala'): ?>
            <div class="input-field" id="imagenField">
                <?php if (!empty($recurso['imagen'])): ?>
                    <div style="margin-bottom: 1rem; text-align: center;">
                        <label style="color: #d4af37; font-weight: bold; display: block; margin-bottom: 0.5rem;">
                            Imagen actual:
                        </label>
                        <img src="../img/salas/<?= htmlspecialchars($recurso['imagen']) ?>" 
                             alt="Imagen actual" 
                             style="max-width: 100%; max-height: 200px; border-radius: 8px; border: 2px solid #d4af37;">
                    </div>
                <?php endif; ?>
                <span class="input-icon"><i class="fas fa-image"></i></span>
                <input class="input" type="file" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp">
                <small style="color: #666; font-size: 0.85rem; display: block; margin-top: 5px;">
                    <?= !empty($recurso['imagen']) ? 'Dejar vacío para mantener la imagen actual' : 'Formatos: JPG, PNG, WEBP' ?>
                </small>
                <div id="imagenError" class="input-error-message 
                    <?php if (isset($_GET['imagenInvalida'])) echo 'active'; ?>">
                    <?php if (isset($_GET['imagenInvalida'])) echo "Formato de imagen no válido"; ?>
                </div>
            </div>
            <?php endif; ?>

            <button class="submit" type="submit" name="editar">Actualizar Recurso</button>
            <div id="clientErrorLogin" class="client-error" aria-live="polite"></div>
        </form>

        <form action="./recursos.php" method="get" class="login-form">
            <button class="submit" type="submit">Volver a Gestión de Recursos</button>
        </form>
    </div>

    </div>
    
    <script>
    function toggleImageField() {
        const tipo = document.getElementById('tipo').value;
        const imagenField = document.getElementById('imagenField');
        if (imagenField) {
            if (tipo === 'sala') {
                imagenField.style.display = 'block';
            } else {
                imagenField.style.display = 'none';
            }
        }
    }
    // Ejecutar al cargar para mostrar/ocultar según tipo actual
    window.addEventListener('DOMContentLoaded', toggleImageField);
    </script>
</body>
</html>
