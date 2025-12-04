<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrar Usuario</title>
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
        <form id="registerForm" action="../proc/proc_register.php" method="post" class="login-form" novalidate>
            <h2 class="form-title">Nuevo Usuario</h2>

            <div class="input-field">
                <span class="input-icon"><i class="fas fa-user"></i></span>
                <input class="input" type="text" id="name" name="name" placeholder="Nombre">
                <div id="nameError" class="input-error-message 
                    <?php 
                        if (isset($_GET['nombreVacio']) || isset($_GET['nombreMal'])) {
                        echo 'active';} 
                    ?>">
                    <?php
                        if (isset($_GET['nombreVacio'])) {
                            echo "El nombre no puede estar vacío";
                        } elseif (isset($_GET['nombreMal'])) {
                            echo "El nombre solo puede contener letras";
                        }
                    ?>
                </div>
            </div>

            <div class="input-field">
                <span class="input-icon"><i class="fas fa-user"></i></span>
                <input class="input" type="text" id="apellido" name="apellido" placeholder="Apellido">
                <div id="apellidoError" class="input-error-message 
                    <?php 
                        if (isset($_GET['apellidoVacio']) || isset($_GET['apellidoMal'])) {
                        echo 'active';} 
                    ?>">
                    <?php
                        if (isset($_GET['apellidoVacio'])) {
                            echo "El apellido no puede estar vacío";
                        } elseif (isset($_GET['apellidoMal'])) {
                            echo "El apellido solo puede contener letras";
                        }
                    ?>
                </div>
            </div>

            <div class="input-field">
                <span class="input-icon"><i class="fas fa-user"></i></span>
                <input class="input" type="text" id="username" name="username" placeholder="Usuario">
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
                        } elseif (isset($_GET['usernameIncorrecto']) || (isset($_GET['error']) && $_GET['error'] === 'usuario_incorrecto')) {
                            echo "El nombre de usuario ya está en uso";
                        }
                    ?>
                </div>
            </div>

            <div class="input-field">
                <span class="input-icon"><i class="fas fa-lock"></i></span>
                <input class="input" type="password" id="password" name="password" placeholder="Contraseña">
                <div id="passwordError" class="input-error-message 
                        <?php 
                            if (isset($_GET['passwordVacio']) || (isset($_GET['passwordCorta']))) {
                            echo 'active';} 
                        ?>">
                        <?php
                            if (isset($_GET['passwordVacio'])) {
                                echo "La contraseña no puede estar vacía";
                            } elseif (isset($_GET['passwordCorta'])) {
                                echo "La contraseña debe tener al menos 6 caracteres";
                            }
                        ?>
                    </div>
            </div>

            <div class="input-field">
                <span class="input-icon"><i class="fas fa-lock"></i></span>
                <input class="input" type="password" id="confirm_password" name="confirm_password" placeholder="Confirmar Contraseña">
                <div id="confirmPasswordError" class="input-error-message 
                        <?php 
                            if (isset($_GET['passwordVacio']) || (isset($_GET['passwordIncorrecta']))) {
                            echo 'active';} 
                        ?>">
                        <?php
                            if (isset($_GET['passwordVacio'])) {
                                echo "La contraseña no puede estar vacía";
                            } elseif (isset($_GET['passwordIncorrecta'])) {
                                echo "Contraseña no coinciden";
                            }
                        ?>
                    </div>
            </div>

            <button class="submit" type="submit" name="login">Registrar</button>
            <div id="clientErrorLogin" class="client-error" aria-live="polite"></div>
        </form>

        <form action="./restaurante.php" method="post" class="login-form">
            <button class="submit" type="submit" name="login">Volver atrás</button>
        </form>
    </div>

    </div>
    <script type="text/javascript" src="../proc/proc_register.js"></script>
</body>
</html>