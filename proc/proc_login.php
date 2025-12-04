<?php
if (!filter_has_var(INPUT_POST, 'login')) {
    header("Location: ../index.php");
    exit();
} else {
    
    $errores = "";

    // USERNAME
    if (isset($_POST['username']) && !empty(trim($_POST['username']))) {
        $username = trim($_POST['username']);
    } else {
        $errores .= ($errores ? '&' : '?') . 'usernameVacio=true';
    }

    if (isset($username) && !preg_match("/^[a-zA-Z0-9]*$/", $username)){
        $errores .= ($errores ? '&' : '?') . 'usernameMal=true';
    }

    // PASSWORD
    if (isset($_POST['password']) && !empty($_POST['password'])) {
        $password = $_POST['password'];
    } else {
        $errores .= ($errores ? '&' : '?') . 'passwordVacio=true';
    }

    if ($errores !== ""){
        $datosRecibidos = array('username' => $username ?? '');
        $datosDevueltos = http_build_query($datosRecibidos);
        header("Location: ../views/login.php".$errores."&".$datosDevueltos);
        exit();
    } else {
        include_once('../includes/conexion.php');

        // Trae también id_usuario, nombre e id_rol
        $sql = "SELECT id_usuario, user, password, nombre, id_rol
                FROM usuarios
                WHERE user = :username
                LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $login = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$login) {
            $datosRecibidos = array('username' => $username);
            $datosDevueltos = http_build_query($datosRecibidos);
            $errores = "?usernameIncorrecto=true";
            header("Location: ../views/login.php".$errores."&".$datosDevueltos);
            exit();
        }

        if (!password_verify($password, $login['password'])) {
            $datosRecibidos = array('username' => $username);
            $datosDevueltos = http_build_query($datosRecibidos);
            $errores = "?passwordIncorrecta=true";
            header("Location: ../views/login.php".$errores."&".$datosDevueltos);
            exit();
        }

        // Login correcto: guarda username, id_usuario, nombre e id_rol
        session_start();
        $_SESSION['user'] = $login['user'];                
        $_SESSION['id_usuario'] = (int)$login['id_usuario'];
        $_SESSION['nombre'] = $login['nombre'];
        $_SESSION['id_rol'] = (int)$login['id_rol'];
        
        // Mantener compatibilidad con código antiguo que usa id_camarero
        $_SESSION['id_camarero'] = (int)$login['id_usuario'];

        header("Location: ../views/restaurante.php");
        exit();
    }
}
