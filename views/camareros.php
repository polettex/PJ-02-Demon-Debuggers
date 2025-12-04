<?php
// Conexión a la base de datos
include '../includes/conexion.php';
session_start();

// Si hay resultados filtrados en sesión, usarlos
if (isset($_SESSION['camareros_filtrados']) && !empty($_SESSION['camareros_filtrados'])) {
    $camareros = $_SESSION['camareros_filtrados'];
    // Limpiar la sesión para que no se quede pegada al refrescar
    unset($_SESSION['camareros_filtrados']);
} else {
    // Consulta para obtener todos los usuarios con rol de camarero (id_rol = 1)
    $query = "SELECT id_usuario, nombre, apellidos, user FROM usuarios WHERE id_rol = 1";
    $stmt = $conn->query($query);
    $camareros = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Camareros</title>
    <link rel="stylesheet" href="../css/styles.css"> 
</head>
<body class="body-camareros">
    <h1>Lista de Camareros</h1>

    <!-- Formulario de filtrado -->
    <form action="../proc/filtrar-camareros.php" method="get">
        <label for="filtro-nombre">Nombre:</label>
        <input type="text" id="filtro-nombre" name="filtro-nombre">
        
        <label for="filtro-apellido">Apellido:</label>
        <input type="text" id="filtro-apellido" name="filtro-apellido">
        
        <button type="submit">Filtrar</button>
    </form>

    <!-- Tabla de resultados -->
    <table class="table-camareros">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($camareros)): ?>
                <?php foreach ($camareros as $camarero): ?>
                    <tr>
                        <td><?= htmlspecialchars($camarero['id_usuario']) ?></td>
                        <td><?= htmlspecialchars($camarero['nombre']) ?></td>
                        <td><?= htmlspecialchars($camarero['apellidos']) ?></td>
                        <td><?= htmlspecialchars($camarero['user']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No se encontraron camareros</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
