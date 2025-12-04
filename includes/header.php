<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ./login.php");
    exit();
}

/* Si no está aún en sesión, lo buscamos una vez y lo guardamos */
if (!isset($_SESSION['id_camarero'])) {
    include_once('conexion.php');

    try {
        // OJO: el campo es 'user' (no 'usuario')
        $stmt = $conn->prepare('SELECT id_camarero, nombre FROM camareros WHERE user = ? LIMIT 1');
        $stmt->execute([ $_SESSION['user'] ]);   // <-- aquí debe ir el username
        $cam = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($cam) {
            $_SESSION['id_camarero'] = (int)$cam['id_camarero'];
            if (empty($_SESSION['nombre']) && !empty($cam['nombre'])) {
                $_SESSION['nombre'] = $cam['nombre'];
            }
        }
    } catch (Exception $e) {
        // silencioso
    }
}
?>
