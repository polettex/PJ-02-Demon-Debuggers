<?php
    include_once('../includes/header.php');
    include_once('../includes/conexion.php');
    include('../proc/proc_restaurante.php');
    
    // Obtener todas las salas desde la base de datos
    try {
        $stmtSalas = $conn->prepare('
            SELECT id_recurso, nombre, imagen 
            FROM recursos 
            WHERE tipo = "sala" 
            ORDER BY id_recurso ASC
        ');
        $stmtSalas->execute();
        $salas = $stmtSalas->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $salas = [];
    }
?>
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salas</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="restaurante_body">
    <div class="global">
        <?php if (count($salas) > 0): ?>
            <?php 
            // Dividir salas en grupos de 3 para las filas
            $chunks = array_chunk($salas, 3);
            $totalChunks = count($chunks);
            ?>
            <?php foreach ($chunks as $index => $chunk): ?>
                <div class="row<?= ($index === $totalChunks - 1) ? ' ' : '' ?>" <?= ($index === $totalChunks - 1) ? 'id="lastrow"' : '' ?>>
                    <?php foreach ($chunk as $sala): ?>
                        <a href="sala.php?sala=<?= (int)$sala['id_recurso'] ?>" 
                           name="item" 
                           id="sala_<?= (int)$sala['id_recurso'] ?>"
                           <?php if (!empty($sala['imagen'])): ?>
                           style="background-image: url('../img/salas/<?= htmlspecialchars($sala['imagen']) ?>');"
                           <?php endif; ?>>
                            <div class="textorows"><?= htmlspecialchars($sala['nombre']) ?></div>
                            <div class="libres"><?php echo mesasOcupadas($sala['id_recurso']); ?></div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-salas">
                <p style="color: #d4af37; font-size: 1.5rem; text-align: center; margin-top: 3rem;">
                    <i class="fas fa-exclamation-circle"></i><br>
                    No hay salas disponibles.<br>
                    <small style="font-size: 0.8rem;">Los administradores pueden crear salas desde Gestión de Recursos.</small>
                </p>
            </div>
        <?php endif; ?>
    </div>   
    <?php
    include_once('../includes/footer.php');
?> 
</body>
</html>
