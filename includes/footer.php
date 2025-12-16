

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
  <a href="../views/reservas.php" class="icon-footer" title="Reservas Anticipadas">
    <i class="fas fa-calendar-alt"></i>
  </a>
  <?php if (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == 4): ?>
  <a href="../views/trabajadores.php" class="icon-footer" title="Gestión de Trabajadores">
    <i class="fas fa-user-cog"></i>
  </a>
  <a href="../views/recursos.php" class="icon-footer" title="Gestión de Recursos">
    <i class="fas fa-boxes"></i>
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

