<?php
    function mesasOcupadas($idSala){
        include('../includes/conexion.php');

        // Contar total de mesas de una sala
        $sqlTotal = "SELECT COUNT(*) as total 
                FROM recursos r
                INNER JOIN recursos_jerarquia rh ON r.id_recurso = rh.id_recurso_hijo
                WHERE rh.id_recurso_padre = :idSala 
                AND r.tipo = 'mesa'";
        $stmtTotal = $conn->prepare($sqlTotal);
        $stmtTotal->bindParam(':idSala', $idSala, PDO::PARAM_INT);
        $stmtTotal->execute();
        $resultTotal = $stmtTotal->fetch(PDO::FETCH_ASSOC);
        $total = $resultTotal['total'];
        
        // Contar mesas libres de una sala específica
        $sqlLibres = "SELECT COUNT(*) as libres 
                FROM recursos r
                INNER JOIN recursos_jerarquia rh ON r.id_recurso = rh.id_recurso_hijo
                WHERE rh.id_recurso_padre = :idSala 
                AND r.tipo = 'mesa' 
                AND r.estado = 'libre'";
        $stmtLibres = $conn->prepare($sqlLibres);
        $stmtLibres->bindParam(':idSala', $idSala, PDO::PARAM_INT);
        $stmtLibres->execute();
        $resultLibres = $stmtLibres->fetch(PDO::FETCH_ASSOC);
        $libres = $resultLibres['libres'];
        
        return $libres . '/' . $total;
    }

?>