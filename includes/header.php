<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ./login.php");
    exit();
}

/* Si no está aún en sesión, lo buscamos una vez y lo guardamos */
if (!isset($_SESSION['id_camarero']) || !isset($_SESSION['id_rol'])) {
    include_once('conexion.php');

    try {
        // Obtener información del usuario incluyendo su rol
        $stmt = $conn->prepare('SELECT u.id_usuario, u.nombre, u.id_rol FROM usuarios u WHERE u.user = ? LIMIT 1');
        $stmt->execute([ $_SESSION['user'] ]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario) {
            $_SESSION['id_camarero'] = (int)$usuario['id_usuario'];
            $_SESSION['id_rol'] = (int)$usuario['id_rol'];
            
            if (empty($_SESSION['nombre']) && !empty($usuario['nombre'])) {
                $_SESSION['nombre'] = $usuario['nombre'];
            }
        }
    } catch (Exception $e) {
        // silencioso
    }
}
?>
