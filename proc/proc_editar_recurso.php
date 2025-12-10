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
$id_recurso = (int)($_POST['id_recurso'] ?? 0);
$tipo = trim($_POST['tipo'] ?? '');
$nombre = trim($_POST['nombre'] ?? '');
$capacidad = !empty($_POST['capacidad']) ? (int)$_POST['capacidad'] : null;
$estado = !empty($_POST['estado']) ? trim($_POST['estado']) : null;
$id_sala = isset($_POST['id_sala']) && $_POST['id_sala'] !== '' ? (int)$_POST['id_sala'] : null;

// Validar campos obligatorios
if ($id_recurso <= 0) {
    header('Location: ../views/recursos.php?error=id_invalido');
    exit;
}

// Obtener imagen actual del recurso
try {
    $stmtCurrent = $conn->prepare('SELECT imagen FROM recursos WHERE id_recurso = ?');
    $stmtCurrent->execute([$id_recurso]);
    $currentData = $stmtCurrent->fetch(PDO::FETCH_ASSOC);
    $imagenActual = $currentData['imagen'] ?? null;
} catch (Exception $e) {
    $imagenActual = null;
}

$nuevaImagen = $imagenActual; // Por defecto mantener la actual

// Inicializar array de errores
$errors = [];

// Validaciones
if (empty($tipo) || !in_array($tipo, ['sala', 'mesa'])) {
    $errors['tipoVacio'] = "Debe seleccionar un tipo válido";
}

if (empty($nombre)) {
    $errors['nombreVacio'] = "El nombre no puede estar vacío";
}

// Procesar nueva imagen solo si es una sala y se subió un archivo
if ($tipo === 'sala' && isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $fileType = $_FILES['imagen']['type'];
    
    if (!in_array($fileType, $allowedTypes)) {
        $errors['imagenInvalida'] = "Formato de imagen no válido";
    } else {
        // Generar nombre único para la nueva imagen
        $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $nuevaImagen = 'sala_' . time() . '_' . uniqid() . '.' . $extension;
        $uploadPath = '../img/salas/' . $nuevaImagen;
        
        // Mover archivo subido
        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $uploadPath)) {
            $errors['imagenError'] = "Error al subir la imagen";
            $nuevaImagen = $imagenActual; // Mantener la actual si falla
        } else {
            // Eliminar imagen anterior si existe y es diferente
            if ($imagenActual && $imagenActual !== $nuevaImagen && file_exists('../img/salas/' . $imagenActual)) {
                unlink('../img/salas/' . $imagenActual);
            }
        }
    }
}

// Si cambia de sala a otro tipo, eliminar la imagen
if ($tipo !== 'sala' && $imagenActual) {
    if (file_exists('../img/salas/' . $imagenActual)) {
        unlink('../img/salas/' . $imagenActual);
    }
    $nuevaImagen = null;
}

// Si hay errores, redirigir
if (!empty($errors)) {
    $query_string = http_build_query($errors);
    header("Location: ../views/editar_recurso.php?id=$id_recurso&$query_string");
    exit();
}

try {
    // Actualizar el recurso
    $stmtUpdate = $conn->prepare('
        UPDATE recursos 
        SET tipo = ?, nombre = ?, capacidad = ?, estado = ?, imagen = ?
        WHERE id_recurso = ?
    ');
    $stmtUpdate->execute([$tipo, $nombre, $capacidad, $estado, $nuevaImagen, $id_recurso]);
    
    // Si es una mesa, actualizar la relación con la sala
    if ($tipo === 'mesa') {
        // Primero eliminar cualquier relación existente
        $stmtDeleteRel = $conn->prepare('DELETE FROM recursos_jerarquia WHERE id_recurso_hijo = ?');
        $stmtDeleteRel->execute([$id_recurso]);
        
        // Si se seleccionó una sala, crear la nueva relación
        if ($id_sala) {
            $stmtInsertRel = $conn->prepare('
                INSERT INTO recursos_jerarquia (id_recurso_padre, id_recurso_hijo) 
                VALUES (?, ?)
            ');
            $stmtInsertRel->execute([$id_sala, $id_recurso]);
        }
    }
    
    // Redirigir con éxito
    header('Location: ../views/recursos.php?success=actualizado');
    exit;
    
} catch (Exception $e) {
    // Si hubo error y se subió una nueva imagen, eliminarla
    if ($nuevaImagen && $nuevaImagen !== $imagenActual && file_exists('../img/salas/' . $nuevaImagen)) {
        unlink('../img/salas/' . $nuevaImagen);
    }
    header('Location: ../views/editar_recurso.php?id=' . $id_recurso . '&error=db_error');
    exit;
}
?>
