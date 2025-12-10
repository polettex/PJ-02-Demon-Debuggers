<?php
session_start();
require_once('../includes/conexion.php');

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['id_usuario'])) {
    exit; // Salir silenciosamente
}

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

// Obtener datos del formulario POST
$id_mesa = isset($_POST['id_mesa']) ? (int)$_POST['id_mesa'] : 0;
$posicion_x = isset($_POST['x']) ? (float)$_POST['x'] : 50.0;
$posicion_y = isset($_POST['y']) ? (float)$_POST['y'] : 50.0;

// Validar datos
if ($id_mesa <= 0) {
    exit;
}

// Limitar valores entre 0 y 100
$posicion_x = max(0, min(100, $posicion_x));
$posicion_y = max(0, min(100, $posicion_y));

try {
    // Actualizar posición de la mesa
    $stmt = $conn->prepare('
        UPDATE recursos 
        SET posicion_x = ?, posicion_y = ? 
        WHERE id_recurso = ? AND tipo = "mesa"
    ');
    $stmt->execute([$posicion_x, $posicion_y, $id_mesa]);
    
    // No enviar salida para iframe oculto
    exit;
    
} catch (Exception $e) {
    // Error silencioso
    exit;
}
?>
