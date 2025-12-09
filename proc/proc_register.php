<?php
// Incluir la conexión a la base de datos
include '../includes/conexion.php';
session_start();

// Verificar que el usuario sea administrador
if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 4) {
    header('Location: ../views/restaurante.php');
    exit;
}

// Verificar si se enviaron los datos mediante POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $name = trim($_POST['name']);
    $apellido = trim($_POST['apellido']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $id_rol = (int)($_POST['id_rol'] ?? 0);

    // Inicializar un array para almacenar errores
    $errors = [];

    // Validaciones
    if (empty($name)) {
        $errors['nombreVacio'] = "El nombre no puede estar vacío";
    } elseif (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\\s]+$/", $name)) {
        $errors['nombreMal'] = "El nombre solo puede contener letras y espacios";
    }

    if (empty($apellido)) {
        $errors['apellidoVacio'] = "El apellido no puede estar vacío";
    } elseif (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\\s]+$/", $apellido)) {
        $errors['apellidoMal'] = "El apellido solo puede contener letras y espacios";
    }

    if (empty($username)) {
        $errors['usernameVacio'] = "El nombre de usuario no puede estar vacío";
    } elseif (!preg_match("/^[a-zA-Z0-9]+$/", $username)) {
        $errors['usernameMal'] = "El nombre de usuario solo puede contener letras y números";
    } elseif ($stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE user = :username")) {
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $errors['usernameIncorrecto'] = "El nombre de usuario ya está en uso";
        }
        
    } else {
        $errors['databaseError'] = "Error al preparar la consulta de verificación de usuario";
    }

    if (empty($password)) {
        $errors['passwordVacio'] = "La contraseña no puede estar vacía";
    } elseif (strlen($password) < 6) {
        $errors['passwordCorta'] = "1";
    }

    if ($password !== $confirm_password) {
        $errors['passwordIncorrecta'] = "Las contraseñas no coinciden";
    }

    if ($id_rol <= 0) {
        $errors['rolVacio'] = "Debe seleccionar un rol";
    }

    // Si hay errores, redirigir al formulario con los mensajes de error
    if (!empty($errors)) {
        $query_string = http_build_query($errors);
        header("Location: ../views/register.php?$query_string");
        exit();
    }

    // Encriptar la contraseña
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Preparar y ejecutar la consulta para insertar el nuevo usuario con el rol seleccionado
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellidos, user, password, id_rol) 
                        VALUES (:nombre, :apellidos, :user, :password, :id_rol)");

    $stmt->bindParam(':nombre', $name);
    $stmt->bindParam(':apellidos', $apellido);
    $stmt->bindParam(':user', $username);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("Location: ../views/trabajadores.php?success=creado");
        exit();
    } else {
        header("Location: ../views/register.php?error=database_error");
        exit();
    }
}
?>