<?php
require_once('../includes/header.php');
require_once('../includes/conexion.php');

echo "<h1>Debug - Salas en Base de Datos</h1>";
echo "<pre>";

try {
    // Ver todas las salas
    $stmt = $conn->prepare('SELECT * FROM recursos WHERE tipo = "sala" ORDER BY id_recurso ASC');
    $stmt->execute();
    $salas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Total de salas encontradas: " . count($salas) . "\n\n";
    
    if (count($salas) > 0) {
        foreach ($salas as $sala) {
            echo "ID: " . $sala['id_recurso'] . "\n";
            echo "Nombre: " . $sala['nombre'] . "\n";
            echo "Tipo: " . $sala['tipo'] . "\n";
            echo "Capacidad: " . ($sala['capacidad'] ?? 'NULL') . "\n";
            echo "Estado: " . ($sala['estado'] ?? 'NULL') . "\n";
            echo "Imagen: " . ($sala['imagen'] ?? 'NULL') . "\n";
            echo "-------------------\n";
        }
    } else {
        echo "No se encontraron salas en la base de datos.\n";
    }
    
    echo "\n\nTODOS los recursos:\n";
    $stmtAll = $conn->prepare('SELECT * FROM recursos ORDER BY tipo, id_recurso');
    $stmtAll->execute();
    $todos = $stmtAll->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($todos as $recurso) {
        echo "ID: {$recurso['id_recurso']} | Tipo: {$recurso['tipo']} | Nombre: {$recurso['nombre']}\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}

echo "</pre>";
echo '<br><a href="restaurante.php">Volver a restaurante</a>';
echo '<br><a href="recursos.php">Ir a gestión de recursos</a>';
?>
