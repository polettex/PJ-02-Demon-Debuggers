<?php
session_start();
require_once('../includes/conexion.php');

echo "<h1>Debug de Sesión</h1>";
echo "<pre>";
echo "Contenido de \$_SESSION:\n";
print_r($_SESSION);
echo "\n\n";

if (isset($_SESSION['user'])) {
    echo "Usuario en sesión: " . $_SESSION['user'] . "\n\n";
    
    // Consultar datos del usuario
    try {
        $stmt = $conn->prepare('SELECT u.id_usuario, u.nombre, u.id_rol, r.nombre as rol_nombre FROM usuarios u INNER JOIN roles r ON u.id_rol = r.id_rol WHERE u.user = ? LIMIT 1');
        $stmt->execute([$_SESSION['user']]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "Datos del usuario en BD:\n";
        print_r($usuario);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

echo "</pre>";

echo '<br><a href="restaurante.php">Volver al restaurante</a>';
echo '<br><a href="../proc/proc_logout.php">Cerrar sesión</a>';
?>
