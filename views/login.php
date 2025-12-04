

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="login-body">

    <div class="logo-container-left">
        <div class="div-logo">
            <img class="logo" src="../img/logo_sin_fondo" alt="logo">
        </div>
    </div>

    <div class="form-container-right">

    <div id="loginFormContainer">
        <form id="loginForm" action="../proc/proc_login.php" method="post" class="login-form" novalidate>
            <h2 class="form-title">Iniciar Sesión</h2>

            <div class="input-field">
                <span class="input-icon "><i class="fas fa-user"></i></span>
                <input class="input 
                    <?php 
                        if (isset($_GET['usernameVacio']) || isset($_GET['usernameIncorrecto']) || (isset($_GET['error']) && $_GET['error'] === 'usuario_incorrecto')) {
                        echo 'invalid';} 
                    ?>" 
                    type="text" id="username" name="username" placeholder="Usuario" required 
                    value="<?php if (isset($_GET['username'])) {
                            echo htmlspecialchars($_GET['username']);
                    } ?>"
                >
                <div id="usernameError" class="input-error-message 
                    <?php 
                        if (isset($_GET['usernameVacio']) || isset($_GET['usernameIncorrecto']) || (isset($_GET['error']) && $_GET['error'] === 'usuario_incorrecto')) {
                        echo 'active';} 
                    ?>">
                    <?php
                        if (isset($_GET['usernameVacio'])) {
                            echo "El nombre de usuario no puede estar vacío";
                        } elseif (isset($_GET['usernameMal'])) {
                            echo "El nombre de usuario solo puede contener letras y números";
                        } elseif (isset($_GET['usernameIncorrecto'])) {
                            echo "Usuario no encontrado";
                        }
                    ?>
                </div>
            </div>
            <div class="input-field">
                <span class="input-icon"><i class="fas fa-lock"></i></span>
                <input class="input
                    <?php 
                        if (isset($_GET['passwordVacio']) || (isset($_GET['passwordIncorrecta']))) {
                        echo 'invalid';} 
                    ?>" 
                    type="password" id="password" name="password" placeholder="Contraseña" required>
                    <div id="passwordError" class="input-error-message 
                        <?php 
                            if (isset($_GET['passwordVacio']) || (isset($_GET['passwordIncorrecta']))) {
                            echo 'active';} 
                        ?>">
                        <?php
                            if (isset($_GET['passwordVacio'])) {
                                echo "La contraseña no puede estar vacía";
                            } elseif (isset($_GET['passwordIncorrecta'])) {
                                echo "Contraseña incorrecta";
                            }
                        ?>
                    </div>
            </div>
            <button class="submit" type="submit" name="login">Iniciar sesión</button>
            <div id="clientErrorLogin" class="client-error" aria-live="polite"></div>
        </form>
        <form action="../index.php" method="post" class="login-form">
            <button class="submit" type="submit" name="login">Volver inicio</button>
        </form>
    </div>
    <script type="text/javascript" src="../proc/proc_login.js"></script>
</body>
