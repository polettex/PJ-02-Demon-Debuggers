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

// Obtener ID del trabajador a eliminar
$id_usuario = (int)($_POST['id_usuario'] ?? 0);

// Validar ID
if ($id_usuario <= 0) {
    header('Location: ../views/trabajadores.php?error=id_invalido');
    exit;
}

// Evitar que el admin se elimine a sí mismo
if ($id_usuario == $_SESSION['id_camarero']) {
    header('Location: ../views/trabajadores.php?error=no_auto_eliminar');
    exit;
}

try {
    // Eliminar el trabajador
    $stmtDelete = $conn->prepare('DELETE FROM usuarios WHERE id_usuario = ?');
    $stmtDelete->execute([$id_usuario]);
    
    // Redirigir con éxito
    header('Location: ../views/trabajadores.php?success=eliminado');
    exit;
    
} catch (Exception $e) {
    header('Location: ../views/trabajadores.php?error=db_error');
    exit;
}
?>
