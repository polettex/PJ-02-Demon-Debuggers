<?php
// Verificar que el usuario sea administrador
require_once('../includes/header.php');
require_once('../includes/conexion.php');

if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 4) {
    header('Location: restaurante.php');
    exit;
}

// Obtener ID del trabajador a editar
$idEditar = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($idEditar <= 0) {
    header('Location: trabajadores.php');
    exit;
}

// Prevenir que el admin se edite a sí mismo
if ($idEditar == $_SESSION['id_camarero']) {
    header('Location: trabajadores.php?error=no_auto_editar');
    exit;
}

// Obtener datos del trabajador
try {
    $stmtTrabajador = $conn->prepare('SELECT * FROM usuarios WHERE id_usuario = ?');
    $stmtTrabajador->execute([$idEditar]);
    $trabajador = $stmtTrabajador->fetch(PDO::FETCH_ASSOC);
    
    if (!$trabajador) {
        header('Location: trabajadores.php?error=no_encontrado');
        exit;
    }
} catch (Exception $e) {
    header('Location: trabajadores.php?error=db_error');
    exit;
}

// Obtener roles disponibles
try {
    $stmtRoles = $conn->prepare('SELECT id_rol, nombre FROM roles ORDER BY id_rol ASC');
    $stmtRoles->execute();
    $roles = $stmtRoles->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $roles = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Usuario</title>
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
        <form id="editForm" action="../proc/proc_editar_trabajador.php" method="post" class="login-form" novalidate>
            <h2 class="form-title">Editar Usuario</h2>

            <input type="hidden" name="id_usuario" value="<?= (int)$trabajador['id_usuario'] ?>">

            <div class="input-field">
                <span class="input-icon"><i class="fas fa-user"></i></span>
                <input class="input" type="text" id="name" name="name" placeholder="Nombre" 
                       value="<?= htmlspecialchars($trabajador['nombre']) ?>">
                <div id="nameError" class="input-error-message 
                    <?php 
                        if (isset($_GET['nombreVacio']) || isset($_GET['nombreMal'])) {
                        echo 'active';} 
                    ?>">
                    <?php
                        if (isset($_GET['nombreVacio'])) {
                            echo "El nombre no puede estar vacío";
                        } elseif (isset($_GET['nombreMal'])) {
                            echo "El nombre solo puede contener letras";
                        }
                    ?>
                </div>
            </div>

            <div class="input-field">
                <span class="input-icon"><i class="fas fa-user"></i></span>
                <input class="input" type="text" id="apellido" name="apellido" placeholder="Apellido"
                       value="<?= htmlspecialchars($trabajador['apellidos']) ?>">
                <div id="apellidoError" class="input-error-message 
                    <?php 
                        if (isset($_GET['apellidoVacio']) || isset($_GET['apellidoMal'])) {
                        echo 'active';} 
                    ?>">
                    <?php
                        if (isset($_GET['apellidoVacio'])) {
                            echo "El apellido no puede estar vacío";
                        } elseif (isset($_GET['apellidoMal'])) {
                            echo "El apellido solo puede contener letras";
                        }
                    ?>
                </div>
            </div>

            <div class="input-field">
                <span class="input-icon"><i class="fas fa-user"></i></span>
                <input class="input" type="text" id="username" name="username" placeholder="Usuario" 
                       value="<?= htmlspecialchars($trabajador['user']) ?>" readonly>
                <small style="color: #666; font-size: 0.85rem; display: block; margin-top: 5px;">
                    El nombre de usuario no se puede modificar
                </small>
            </div>

            <div class="input-field">
                <span class="input-icon"><i class="fas fa-lock"></i></span>
                <input class="input" type="password" id="password" name="password" placeholder="Nueva Contraseña (opcional)">
                <small style="color: #666; font-size: 0.85rem; display: block; margin-top: 5px;">
                    Dejar en blanco para mantener la contraseña actual
                </small>
                <div id="passwordError" class="input-error-message 
                        <?php 
                            if (isset($_GET['passwordCorta'])) {
                            echo 'active';} 
                        ?>">
                        <?php
                            if (isset($_GET['passwordCorta'])) {
                                echo "La contraseña debe tener al menos 6 caracteres";
                            }
                        ?>
                    </div>
            </div>

            <div class="input-field">
                <span class="input-icon"><i class="fas fa-lock"></i></span>
                <input class="input" type="password" id="confirm_password" name="confirm_password" placeholder="Confirmar Nueva Contraseña">
                <div id="confirmPasswordError" class="input-error-message 
                        <?php 
                            if (isset($_GET['passwordIncorrecta'])) {
                            echo 'active';} 
                        ?>">
                        <?php
                            if (isset($_GET['passwordIncorrecta'])) {
                                echo "Las contraseñas no coinciden";
                            }
                        ?>
                    </div>
            </div>

            <div class="input-field">
                <span class="input-icon"><i class="fas fa-user-tag"></i></span>
                <select class="input" id="id_rol" name="id_rol" required>
                    <option value="">Seleccionar rol...</option>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?= (int)$rol['id_rol'] ?>" 
                                <?= ($trabajador['id_rol'] == $rol['id_rol']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($rol['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div id="rolError" class="input-error-message 
                    <?php 
                        if (isset($_GET['rolVacio'])) {
                        echo 'active';} 
                    ?>">
                    <?php
                        if (isset($_GET['rolVacio'])) {
                            echo "Debe seleccionar un rol";
                        }
                    ?>
                </div>
            </div>

            <button class="submit" type="submit" name="editar">Actualizar Usuario</button>
            <div id="clientErrorLogin" class="client-error" aria-live="polite"></div>
        </form>

        <form action="./trabajadores.php" method="get" class="login-form">
            <button class="submit" type="submit">Volver a Gestión de Trabajadores</button>
        </form>
    </div>

    </div>
</body>
</html>
