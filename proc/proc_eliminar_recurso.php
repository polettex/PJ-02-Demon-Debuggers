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
    header('Location: ../views/recursos.php');
    exit;
}

// Obtener ID del recurso a eliminar
$id_recurso = (int)($_POST['id_recurso'] ?? 0);

// Validar ID
if ($id_recurso <= 0) {
    header('Location: ../views/recursos.php?error=id_invalido');
    exit;
}

try {
    // Verificar si el recurso tiene relaciones en recursos_jerarquia
    $stmtCheck = $conn->prepare('
        SELECT COUNT(*) as count 
        FROM recursos_jerarquia 
        WHERE id_recurso_hijo = ? OR id_recurso_padre = ?
    ');
    $stmtCheck->execute([$id_recurso, $id_recurso]);
    $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] > 0) {
        header('Location: ../views/recursos.php?error=tiene_relaciones');
        exit;
    }
    
    // Eliminar el recurso
    $stmtDelete = $conn->prepare('DELETE FROM recursos WHERE id_recurso = ?');
    $stmtDelete->execute([$id_recurso]);
    
    // Redirigir con éxito
    header('Location: ../views/recursos.php?success=eliminado');
    exit;
    
} catch (Exception $e) {
    header('Location: ../views/recursos.php?error=db_error');
    exit;
}
?>
