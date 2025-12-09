<?php
session_start();
require_once('../includes/conexion.php');

// Verificar que el usuario sea administrador
if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 4) {
    header('Location: ../views/restaurante.php');
    exit;
}

// Verificar que se recibieron los datos del formulario
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/trabajadores.php');
    exit;
}

// Obtener datos del formulario
$id_usuario = (int)($_POST['id_usuario'] ?? 0);
$nombre = trim($_POST['name'] ?? '');
$apellidos = trim($_POST['apellido'] ?? '');
$password = trim($_POST['password'] ?? '');
$confirm_password = trim($_POST['confirm_password'] ?? '');
$id_rol = (int)($_POST['id_rol'] ?? 0);

// Validar campos obligatorios
if ($id_usuario <= 0 || empty($nombre) || empty($apellidos) || $id_rol <= 0) {
    header('Location: ../views/editar_trabajador.php?id=' . $id_usuario . '&error=campos_vacios');
    exit;
}

// Prevenir que el admin se edite a sí mismo
if ($id_usuario == $_SESSION['id_camarero']) {
    header('Location: ../views/trabajadores.php?error=no_auto_editar');
    exit;
}

// Inicializar array de errores
$errors = [];

// Validaciones
if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\\s]+$/", $nombre)) {
    $errors['nombreMal'] = "El nombre solo puede contener letras y espacios";
}

if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\\s]+$/", $apellidos)) {
    $errors['apellidoMal'] = "El apellido solo puede contener letras y espacios";
}

// Validar contraseña solo si se proporcionó
$updatePassword = false;
if (!empty($password)) {
    if (strlen($password) < 6) {
        $errors['passwordCorta'] = "La contraseña debe tener al menos 6 caracteres";
    } elseif ($password !== $confirm_password) {
        $errors['passwordIncorrecta'] = "Las contraseñas no coinciden";
    } else {
        $updatePassword = true;
    }
}

// Si hay errores, redirigir
if (!empty($errors)) {
    $query_string = http_build_query($errors);
    header("Location: ../views/editar_trabajador.php?id=$id_usuario&$query_string");
    exit();
}

try {
    // Si se debe actualizar la contraseña
    if ($updatePassword) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $stmtUpdate = $conn->prepare('
            UPDATE usuarios 
            SET nombre = ?, apellidos = ?, id_rol = ?, password = ?
            WHERE id_usuario = ?
        ');
        $stmtUpdate->execute([$nombre, $apellidos, $id_rol, $passwordHash, $id_usuario]);
    } else {
        // Solo actualizar datos sin contraseña
        $stmtUpdate = $conn->prepare('
            UPDATE usuarios 
            SET nombre = ?, apellidos = ?, id_rol = ?
            WHERE id_usuario = ?
        ');
        $stmtUpdate->execute([$nombre, $apellidos, $id_rol, $id_usuario]);
    }
    
    // Redirigir con éxito
    header('Location: ../views/trabajadores.php?success=actualizado');
    exit;
    
} catch (Exception $e) {
    header('Location: ../views/editar_trabajador.php?id=' . $id_usuario . '&error=db_error');
    exit;
}
?>
