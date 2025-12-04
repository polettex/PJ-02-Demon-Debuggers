<?php

$servername = "localhost:3306";
$dbusername = "root";
$dbpassword = "aa105f81";
$dbname = "db_restaurante"; 

try {
	$conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $dbusername, $dbpassword);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    error_log($e->getMessage());
    die('Error de conexión a la base de datos.');
}

?>