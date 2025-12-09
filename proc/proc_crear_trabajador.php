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
$nombre = trim($_POST['nombre'] ?? '');
$apellidos = trim($_POST['apellidos'] ?? '');
$user = trim($_POST['user'] ?? '');
$password = $_POST['password'] ?? '';
$id_rol = (int)($_POST['id_rol'] ?? 0);

// Validar campos obligatorios
if (empty($nombre) || empty($apellidos) || empty($user) || empty($password) || $id_rol <= 0) {
    header('Location: ../views/trabajadores.php?error=campos_vacios');
    exit;
}

try {
    // Verificar que el usuario no exista ya
    $stmtCheck = $conn->prepare('SELECT id_usuario FROM usuarios WHERE user = ?');
    $stmtCheck->execute([$user]);
    if ($stmtCheck->fetch()) {
        header('Location: ../views/trabajadores.php?error=usuario_existe');
        exit;
    }
    
    // Hashear la contraseña
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    
    // Insertar el nuevo trabajador
    $stmtInsert = $conn->prepare('
        INSERT INTO usuarios (nombre, apellidos, user, password, id_rol) 
        VALUES (?, ?, ?, ?, ?)
    ');
    $stmtInsert->execute([$nombre, $apellidos, $user, $passwordHash, $id_rol]);
    
    // Redirigir con éxito
    header('Location: ../views/trabajadores.php?success=creado');
    exit;
    
} catch (Exception $e) {
    header('Location: ../views/trabajadores.php?error=db_error');
    exit;
}
?>
