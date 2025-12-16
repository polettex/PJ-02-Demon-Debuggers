<?php
session_start();
require_once('../includes/conexion.php');

if (!isset($_SESSION['user']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit();
}

$id_reserva = (int)$_POST['id_reserva'];
$nombre_cliente = $_POST['nombre_cliente'];
$fecha = $_POST['fecha'];
$hora_inicio = $_POST['hora'];
$personas = (int)$_POST['personas'];
$id_recurso = (int)$_POST['id_recurso'];

$hora_final = date('H:i', strtotime($hora_inicio . ' + 1 hour'));

// Validaciones PHP
if (empty($nombre_cliente) || empty($fecha) || empty($hora_inicio) || empty($personas) || empty($id_recurso)) {
    header("Location: ../views/reservas.php?error=campos_vacios");
    exit();
}

if ($fecha < date('Y-m-d')) {
    header("Location: ../views/reservas.php?error=fecha_invalida");
    exit();
}

try {
    // Double check availability (excluding current reservation)
    $stmt = $conn->prepare("
        SELECT id_reserva FROM reservas 
        WHERE id_recurso = ? 
        AND fecha = ? 
        AND id_reserva != ?
        AND (
            (hora_inicio <= ? AND hora_final > ?) OR
            (hora_inicio < ? AND hora_final >= ?) OR
            (hora_inicio >= ? AND hora_final <= ?)
        )
    ");
    $stmt->execute([$id_recurso, $fecha, $id_reserva, $hora_inicio, $hora_inicio, $hora_final, $hora_final, $hora_inicio, $hora_final]);
    
    if ($stmt->rowCount() > 0) {
        header("Location: ../views/reservas.php?error=ocupado");
        exit();
    }

    // Update
    $stmt = $conn->prepare("
        UPDATE reservas 
        SET id_recurso = ?, fecha = ?, hora_inicio = ?, hora_final = ?, nombre_cliente = ?, personas = ?
        WHERE id_reserva = ?
    ");
    $stmt->execute([$id_recurso, $fecha, $hora_inicio, $hora_final, $nombre_cliente, $personas, $id_reserva]);
    
    header("Location: ../views/reservas.php?success=editado");

} catch (PDOException $e) {
    header("Location: ../views/reservas.php?error=db_error");
}
?>
