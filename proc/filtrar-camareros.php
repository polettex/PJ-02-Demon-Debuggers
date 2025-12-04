<?php
// Conexión a la base de datos
include '../includes/conexion.php';

// Obtener los parámetros de filtro
$filtro_nombre = isset($_GET['filtro-nombre']) ? $_GET['filtro-nombre'] : '';
$filtro_apellido = isset($_GET['filtro-apellido']) ? $_GET['filtro-apellido'] : '';


// Construir la consulta SQL con parámetros dinámicos
// Filtrar solo usuarios con rol de camarero (id_rol = 1)
$query = "SELECT id_usuario, nombre, apellidos, user FROM usuarios WHERE id_rol = 1";
$params = [];
$conditions = [];

if (!empty($filtro_nombre)) {
    $conditions[] = "nombre LIKE :nombre";
    $params[':nombre'] = "%$filtro_nombre%";
}

if (!empty($filtro_apellido)) {
    $conditions[] = "apellidos LIKE :apellido";
    $params[':apellido'] = "%$filtro_apellido%";
}



if (!empty($conditions)) {
    $query .= " AND " . implode(" AND ", $conditions);
}

// Preparar y ejecutar la consulta
$stmt = $conn->prepare($query);
$stmt->execute($params);

// Guardar resultados en sesión
session_start();
$_SESSION['camareros_filtrados'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Redirigir a la vista
header('Location: ../views/camareros.php');
exit();
?>
