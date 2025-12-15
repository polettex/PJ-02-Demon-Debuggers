<?php
session_start();
$_SESSION['user'] = 'admin';
$_SESSION['id_camarero'] = 1;
$_SESSION['id_rol'] = 4;
$_SESSION['nombre'] = 'Admin';
header("Location: sala.php?sala=1");
exit();
?>
