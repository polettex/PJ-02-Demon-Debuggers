<head>
<link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="../css/styles.css">
</head>

<footer class="main-footer">
<div class="footer-left">
  <form action="../proc/proc_logout.php" method="post">
    <button type="submit" name="logout" class="icon-footer">
      <i class="fas fa-sign-out-alt"></i>  
    </button>
  </form>
  <a href="../views/historial.php" class="icon-footer">
    <i class="fas fa-clipboard"></i>
  </a>
  <a href="../views/restaurante.php" class="icon-footer">
    <i class="fas fa-chevron-left"></i>
  </a>
  <?php if (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == 4): ?>
  <a href="../views/trabajadores.php" class="icon-footer" title="Gestión de Trabajadores">
    <i class="fas fa-user-cog"></i>
  </a>
  <?php endif; ?>
</div>
  <div class="footer-center">
    Demon Deburgers
  </div>
  <div class="footer-right">
    <img src="../img/logo_sin_fondo.png" alt="Logo Demon Deburgers" class="footer-logo">
  </div>
</footer>

