<?php
    include_once('../includes/header.php');
    include('../proc/proc_restaurante.php');
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
        <div class="row">
            <a href="sala.php?sala=1" name="item" id="P1"><div class="textorows">Patio 1</div><div class="libres"><?php echo mesasOcupadas(1);?></div></a>
            <a href="sala.php?sala=2" name="item" id="P2"><div class="textorows">Patio 2</div><div class="libres"><?php echo mesasOcupadas(2);?></div></a>
            <a href="sala.php?sala=3" name="item" id="P3"><div class="textorows">Patio 3</div><div class="libres"><?php echo mesasOcupadas(3);?></div></a>
        </div>
        <div class="row">
            <a href="sala.php?sala=4" name="item" id="C1"><div class="textorows">Comedor 1</div><div class="libres"><?php echo mesasOcupadas(4);?></div></a>
            <a href="sala.php?sala=5" name="item" id="C2"><div class="textorows">Comedor 2</div><div class="libres"><?php echo mesasOcupadas(5);?></div></a>
            <a href="sala.php?sala=6" name="item" id="PR1"><div class="textorows">Priv 1</div><div class="libres"><?php echo mesasOcupadas(6);?></div></a>
        </div>
        <div class="row" id="lastrow">
            <a href="sala.php?sala=7" name="item" id="PR2"><div class="textorows">Priv 2</div><div class="libres"><?php echo mesasOcupadas(7);?></div></a>
            <a href="sala.php?sala=8" name="item" id="PR3"><div class="textorows">Priv 3</div><div class="libres"><?php echo mesasOcupadas(8);?></div></a>
            <a href="sala.php?sala=9" name="item" id="PR4"><div class="textorows">Priv 4</div><div class="libres"><?php echo mesasOcupadas(9);?></div></a>
        </div>
    </div>   
    <?php
    include_once('../includes/footer.php');
?> 
</body>
</html>

