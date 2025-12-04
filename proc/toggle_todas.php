<?php
require_once '../includes/conexion.php';
session_start();

/* Exigir usuario logueado */
if (!isset($_SESSION['id_usuario']) || (int)$_SESSION['id_usuario'] <= 0) {
    // Compatibilidad con id_camarero
    if (!isset($_SESSION['id_camarero']) || (int)$_SESSION['id_camarero'] <= 0) {
        header('Location: ../views/restaurante.php');
        exit;
    }
    $idUsuario = (int)$_SESSION['id_camarero'];
} else {
    $idUsuario = (int)$_SESSION['id_usuario'];
}

/* Sala */
$salaId = isset($_POST['sala']) ? (int)$_POST['sala'] : 0;
if ($salaId <= 0) {
    header('Location: ../views/restaurante.php');
    exit;
}

try {
    $conn->beginTransaction();

    // 1) Verificar cuántas mesas están ocupadas en esta sala
    $stmt = $conn->prepare('
        SELECT COUNT(*) 
        FROM recursos r
        INNER JOIN recursos_jerarquia rh ON r.id_recurso = rh.id_recurso_hijo
        WHERE rh.id_recurso_padre = ? 
        AND r.tipo = "mesa" 
        AND r.estado = "ocupado"
    ');
    $stmt->execute([$salaId]);
    $ocupadas = (int)$stmt->fetchColumn();

    $stmtTotal = $conn->prepare('
        SELECT COUNT(*) 
        FROM recursos r
        INNER JOIN recursos_jerarquia rh ON r.id_recurso = rh.id_recurso_hijo
        WHERE rh.id_recurso_padre = ? 
        AND r.tipo = "mesa"
    ');
    $stmtTotal->execute([$salaId]);
    $total = (int)$stmtTotal->fetchColumn();

    // 2) Decidir acción: si todas ocupadas → liberar, si no → ocupar
    if ($ocupadas === $total && $total > 0) {
        // Liberar todas las mesas de esta sala
        $upd = $conn->prepare('
            UPDATE recursos r
            INNER JOIN recursos_jerarquia rh ON r.id_recurso = rh.id_recurso_hijo
            SET r.estado = "libre" 
            WHERE rh.id_recurso_padre = ? 
            AND r.tipo = "mesa"
        ');
        $upd->execute([$salaId]);

        // Cerrar reservas abiertas de mesas de esta sala
        $close = $conn->prepare('
            UPDATE reservas res
            INNER JOIN recursos r ON res.id_recurso = r.id_recurso
            INNER JOIN recursos_jerarquia rh ON r.id_recurso = rh.id_recurso_hijo
            SET res.hora_final = CURTIME() 
            WHERE rh.id_recurso_padre = ? 
            AND r.tipo = "mesa"
            AND res.hora_final IS NULL
        ');
        $close->execute([$salaId]);

    } else {
        // Ocupar todas las mesas de esta sala
        $upd = $conn->prepare('
            UPDATE recursos r
            INNER JOIN recursos_jerarquia rh ON r.id_recurso = rh.id_recurso_hijo
            SET r.estado = "ocupado" 
            WHERE rh.id_recurso_padre = ? 
            AND r.tipo = "mesa"
        ');
        $upd->execute([$salaId]);

        // Insertar reservas si no existen
        $stmtMesas = $conn->prepare('
            SELECT r.id_recurso 
            FROM recursos r
            INNER JOIN recursos_jerarquia rh ON r.id_recurso = rh.id_recurso_hijo
            WHERE rh.id_recurso_padre = ? 
            AND r.tipo = "mesa"
        ');
        $stmtMesas->execute([$salaId]);
        $mesas = $stmtMesas->fetchAll(PDO::FETCH_ASSOC);

        foreach ($mesas as $m) {
            $mesaId = (int)$m['id_recurso'];
            $chk = $conn->prepare('SELECT COUNT(*) FROM reservas WHERE id_recurso = ? AND hora_final IS NULL');
            $chk->execute([$mesaId]);
            if ((int)$chk->fetchColumn() === 0) {
                $ins = $conn->prepare('
                    INSERT INTO reservas (id_usuario, id_recurso, fecha, hora_inicio, hora_final) 
                    VALUES (?, ?, CURDATE(), CURTIME(), NULL)
                ');
                $ins->execute([$idUsuario, $mesaId]);
            }
        }
    }

    $conn->commit();
    header('Location: ../views/sala.php?sala=' . $salaId);
    exit;

} catch (Exception $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    header('Location: ../views/restaurante.php?error=toggle_todas');
    exit;
}
