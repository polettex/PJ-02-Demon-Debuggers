<?php
session_start();
require_once('../includes/conexion.php');

// Verificar que el usuario sea administrador
if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 4) {
    header('Location: ../views/restaurante.php');
    exit;
}

// Verificar que se recibieron los datos del formulario
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/recursos.php');
    exit;
}

// Obtener datos del formulario
$tipo = trim($_POST['tipo'] ?? '');
$nombre = trim($_POST['nombre'] ?? '');
$capacidad = !empty($_POST['capacidad']) ? (int)$_POST['capacidad'] : null;
$estado = !empty($_POST['estado']) ? trim($_POST['estado']) : null;
$id_sala = !empty($_POST['id_sala']) ? (int)$_POST['id_sala'] : null;
$imagen = null;

// Inicializar array de errores
$errors = [];

// Validaciones
if (empty($tipo) || !in_array($tipo, ['sala', 'mesa'])) {
    $errors['tipoVacio'] = "Debe seleccionar un tipo válido";
}

if (empty($nombre)) {
    $errors['nombreVacio'] = "El nombre no puede estar vacío";
}

// Procesar imagen solo si es una sala
if ($tipo === 'sala' && isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $fileType = $_FILES['imagen']['type'];
    
    if (!in_array($fileType, $allowedTypes)) {
        $errors['imagenInvalida'] = "Formato de imagen no válido";
    } else {
        // Generar nombre único para la imagen
        $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $imagen = 'sala_' . time() . '_' . uniqid() . '.' . $extension;
        $uploadPath = '../img/salas/' . $imagen;
        
        // Mover archivo subido
        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $uploadPath)) {
            $errors['imagenError'] = "Error al subir la imagen";
            $imagen = null;
        }
    }
}

// Si hay errores, redirigir
if (!empty($errors)) {
    $query_string = http_build_query($errors);
    header("Location: ../views/crear_recurso.php?$query_string");
    exit();
}

try {
    // Insertar el nuevo recurso
    $stmt = $conn->prepare('
        INSERT INTO recursos (tipo, nombre, capacidad, estado, imagen) 
        VALUES (?, ?, ?, ?, ?)
    ');
    $stmt->execute([$tipo, $nombre, $capacidad, $estado, $imagen]);
    
    // Obtener el ID del recurso recién creado
    $id_recurso_nuevo = $conn->lastInsertId();
    
    // Si es una mesa y se seleccionó una sala, crear la relación en recursos_jerarquia
    if ($tipo === 'mesa' && $id_sala) {
        $stmtJerarquia = $conn->prepare('
            INSERT INTO recursos_jerarquia (id_recurso_padre, id_recurso_hijo) 
            VALUES (?, ?)
        ');
        $stmtJerarquia->execute([$id_sala, $id_recurso_nuevo]);
    }
    
    // Redirigir con éxito
    header('Location: ../views/recursos.php?success=creado');
    exit;
    
} catch (Exception $e) {
    // Si hubo error y se subió una imagen, eliminarla
    if ($imagen && file_exists('../img/salas/' . $imagen)) {
        unlink('../img/salas/' . $imagen);
    }
    header('Location: ../views/crear_recurso.php?error=db_error');
    exit;
}
?>
