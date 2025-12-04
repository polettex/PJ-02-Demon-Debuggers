<?php
    function mesasOcupadas($idSala){
        include('../includes/conexion.php');

        // Contar mesas libres de una sala específica usando la tabla de jerarquía
        $sql = "SELECT COUNT(*) as libres 
                FROM recursos r
                INNER JOIN recursos_jerarquia rh ON r.id_recurso = rh.id_recurso_hijo
                WHERE rh.id_recurso_padre = :idSala 
                AND r.tipo = 'mesa' 
                AND r.estado = 'libre'";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idSala', $idSala, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['libres'];
    }

?>