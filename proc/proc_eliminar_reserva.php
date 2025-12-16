<?php
session_start();
require_once('../includes/conexion.php');

if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_reserva'])) {
    $id = (int)$_POST['id_reserva'];
    
    try {
        $stmt = $conn->prepare("DELETE FROM reservas WHERE id_reserva = ?");
        $stmt->execute([$id]);
        header("Location: ../views/reservas.php?success=eliminado");
    } catch (PDOException $e) {
        header("Location: ../views/reservas.php?error=db_error");
    }
} else {
    header("Location: ../views/reservas.php");
}
?>
