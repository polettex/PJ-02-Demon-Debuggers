<?php

//Cerrar sesión y redirigir al login
if (isset($_POST['logout'])) {
    session_start();
    session_unset();
    session_destroy();
    header("Location: ../views/login.php");
    exit();
}

?>