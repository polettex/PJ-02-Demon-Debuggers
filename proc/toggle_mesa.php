<?php
// Carga la conexión PDO ($conn)
require_once '../includes/conexion.php';

// Inicia la sesión para poder leer datos del usuario
session_start();

/* Exigir usuario logueado */
// Si no existe id_usuario en sesión o es inválido, redirige al restaurante
if (!isset($_SESSION['id_usuario']) || (int)$_SESSION['id_usuario'] <= 0) {
    // Compatibilidad: también verificar id_camarero
    if (!isset($_SESSION['id_camarero']) || (int)$_SESSION['id_camarero'] <= 0) {
        header('Location: ../views/restaurante.php');
        exit;
    }
    // Si existe id_camarero pero no id_usuario, usar id_camarero
    $idUsuario = (int)$_SESSION['id_camarero'];
} else {
    $idUsuario = (int)$_SESSION['id_usuario'];
}

/* Parámetros */
// Sala puede venir por POST (preferido) o por GET como alternativa
$salaId  = isset($_POST['sala'])   ? (int)$_POST['sala']
          : (isset($_GET['sala'])  ? (int)$_GET['sala']   : 0);

// Id de mesa a alternar (toggle) también puede venir por POST o GET
$mesaId  = isset($_POST['toggle']) ? (int)$_POST['toggle']
          : (isset($_GET['toggle']) ? (int)$_GET['toggle'] : 0);

// Si alguno de los dos IDs no es válido, vuelve a la vista principal
if ($salaId <= 0 || $mesaId <= 0) {
    header('Location: ../views/restaurante.php');
    exit;
}

/* Leer estado actual */
try {
    // Consulta para obtener el estado actual del recurso mesa y verificar que pertenece a la sala
    $stmt = $conn->prepare('
        SELECT r.estado, r.capacidad 
        FROM recursos r
        INNER JOIN recursos_jerarquia rh ON r.id_recurso = rh.id_recurso_hijo
        WHERE r.id_recurso = ? 
        AND r.tipo = "mesa"
        AND rh.id_recurso_padre = ?
    ');
    $stmt->execute([$mesaId, $salaId]);

    // Recupera la fila (la mesa)
    $mesa = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si no existe la mesa, redirige
    if (!$mesa) {
        header('Location: ../views/restaurante.php');
        exit;
    }
} catch (Exception $e) {
    // Si hay cualquier error en la consulta, redirige
    header('Location: ../views/restaurante.php');
    exit;
}

/* Alternar y registrar */
try {
    // Inicia una transacción para asegurar consistencia de mesa + historial
    $conn->beginTransaction();

    // Normaliza el estado actual a 'ocupado' o 'libre'
    $estadoActual = ($mesa['estado'] === 'ocupado') ? 'ocupado' : 'libre';

    // Calcula el nuevo estado: si estaba ocupado → libre, si no → ocupado
    $nuevoEstado  = ($estadoActual === 'ocupado') ? 'libre' : 'ocupado';

    // 1) Actualizar recurso mesa con el nuevo estado
    $upd = $conn->prepare('UPDATE recursos SET estado = ? WHERE id_recurso = ? AND tipo = "mesa"');
    $upd->execute([$nuevoEstado, $mesaId]);

    // 2) Gestionar historial de reservas
    if ($nuevoEstado === 'ocupado') {
        // Si pasa a "ocupado":
        // Comprobar si ya hay una reserva abierta (sin hora_final) para esa mesa
        $chk = $conn->prepare('SELECT COUNT(*) FROM reservas WHERE id_recurso = ? AND hora_final IS NULL');
        $chk->execute([$mesaId]);

        // Solo insertar una nueva reserva si NO hay ninguna abierta
        if ((int)$chk->fetchColumn() === 0) {
            $ins = $conn->prepare('
                INSERT INTO reservas (id_usuario, id_recurso, fecha, hora_inicio, hora_final, nombre_cliente, personas)
                VALUES (?, ?, CURDATE(), CURTIME(), NULL, "Cliente Casual", ?)
            ');
            // Guarda la reserva con fecha y hora de inicio actual y sin hora de fin
            // Usamos la capacidad de la mesa como valor por defecto para personas
            $ins->execute([$idUsuario, $mesaId, $mesa['capacidad']]);
        }
    } else {
        // Si pasa a "libre":
        // Cerrar todas las reservas abiertas de esa mesa (hora_final IS NULL)
        $close = $conn->prepare('
            UPDATE reservas
            SET hora_final = CURTIME()
            WHERE id_recurso = ? AND hora_final IS NULL
        ');
        $close->execute([$mesaId]);
    }

    // Confirma la transacción (aplica todos los cambios)
    $conn->commit();
} catch (Exception $e) {
    // Si hay error, deshacer cambios pendientes si la transacción sigue abierta
    if ($conn->inTransaction()) $conn->rollBack();

    // Y volver al restaurante
    header('Location: ../views/restaurante.php');
    exit;
}

/* Redirección robusta (sin confiar en inputs) */
// Después de alternar el estado, vuelve a la vista de la sala correspondiente
header('Location: ../views/sala.php?sala=' . $salaId);
exit;