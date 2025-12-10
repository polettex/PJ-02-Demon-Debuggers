<?php
// Verificar que el usuario sea administrador
require_once('../includes/header.php');
require_once('../includes/conexion.php');

if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 4) {
    header('Location: restaurante.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crear Recurso</title>
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
        <form id="createForm" action="../proc/proc_crear_recurso.php" method="post" enctype="multipart/form-data" class="login-form" novalidate>
            <h2 class="form-title">Crear Recurso</h2>

            <div class="input-field">
                <span class="input-icon"><i class="fas fa-tag"></i></span>
                <select class="input" id="tipo" name="tipo" required onchange="toggleImageField()">
                    <option value="">Seleccionar tipo...</option>
                    <option value="sala">🏠 Sala</option>
                    <option value="mesa">🪑 Mesa</option>
                </select>
                <div id="tipoError" class="input-error-message 
                    <?php if (isset($_GET['tipoVacio'])) echo 'active'; ?>">
                    <?php if (isset($_GET['tipoVacio'])) echo "Debe seleccionar un tipo"; ?>
                </div>
            </div>

            <div class="input-field">
                <span class="input-icon"><i class="fas fa-signature"></i></span>
                <input class="input" type="text" id="nombre" name="nombre" placeholder="Nombre" required>
                <div id="nombreError" class="input-error-message 
                    <?php if (isset($_GET['nombreVacio'])) echo 'active'; ?>">
                    <?php if (isset($_GET['nombreVacio'])) echo "El nombre no puede estar vacío"; ?>
                </div>
            </div>

            <div class="input-field">
                <span class="input-icon"><i class="fas fa-users"></i></span>
                <input class="input" type="number" id="capacidad" name="capacidad" placeholder="Capacidad (opcional)" min="0">
                <small style="color: #666; font-size: 0.85rem; display: block; margin-top: 5px;">
                    Número de personas o elementos que puede contener
                </small>
            </div>

            <div class="input-field" id="salaField" style="display: none;">
                <span class="input-icon"><i class="fas fa-door-open"></i></span>
                <select class="input" id="id_sala" name="id_sala">
                    <option value="">Seleccionar sala...</option>
                    <?php
                    try {
                        $stmtSalas = $conn->prepare('SELECT id_recurso, nombre FROM recursos WHERE tipo = "sala" ORDER BY nombre ASC');
                        $stmtSalas->execute();
                        $salasDisponibles = $stmtSalas->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($salasDisponibles as $sala) {
                            echo '<option value="' . (int)$sala['id_recurso'] . '">' . htmlspecialchars($sala['nombre']) . '</option>';
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

            <div class="input-field">
                <span class="input-icon"><i class="fas fa-toggle-on"></i></span>
                <select class="input" id="estado" name="estado">
                    <option value="">Sin estado</option>
                    <option value="libre">Libre</option>
                    <option value="ocupado">Ocupado</option>
                </select>
                <small style="color: #666; font-size: 0.85rem; display: block; margin-top: 5px;">
                    Opcional - Aplica principalmente a mesas y salas
                </small>
            </div>

            <div class="input-field" id="imagenField" style="display: none;">
                <span class="input-icon"><i class="fas fa-image"></i></span>
                <input class="input" type="file" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp">
                <small style="color: #666; font-size: 0.85rem; display: block; margin-top: 5px;">
                    Solo para salas - Formatos: JPG, PNG, WEBP
                </small>
                <div id="imagenError" class="input-error-message 
                    <?php if (isset($_GET['imagenInvalida'])) echo 'active'; ?>">
                    <?php if (isset($_GET['imagenInvalida'])) echo "Formato de imagen no válido"; ?>
                </div>
            </div>

            <button class="submit" type="submit" name="crear">Crear Recurso</button>
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
        const salaField = document.getElementById('salaField');
        
        if (tipo === 'sala') {
            imagenField.style.display = 'block';
            salaField.style.display = 'none';
        } else if (tipo === 'mesa') {
            imagenField.style.display = 'none';
            salaField.style.display = 'block';
        } else {
            imagenField.style.display = 'none';
            salaField.style.display = 'none';
        }
    }
    </script>
</body>
</html>
