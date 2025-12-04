document.getElementById("username").onblur = validarNombre;
document.getElementById("password").onblur = validarPassword;

document.getElementById("loginForm").onsubmit = function (event) {
    // Ejecutamos las validaciones
    validarNombre();
    validarPassword();

    // Si hay errores, evitamos el envío
    usernameError = document.getElementById("usernameError").innerText;
    passwordError = document.getElementById("passwordError").innerText;

    if (usernameError !== "" || passwordError !== "") {
        errorBox = document.getElementById("clientErrorLogin");
        event.preventDefault(); // bloquea el envío
        errorBox.classList.add("active");   // muestra recuadro
        document.getElementById("clientErrorLogin").innerText =
            "Por favor corrige los errores antes de continuar.";
    }
};

function validarNombre() {
    usernameInput = document.getElementById("username");
    nombre = usernameInput.value;
    regex = /^[a-zA-Z0-9]+$/; // solo letras y números
    errorBox = document.getElementById("usernameError");

    if (nombre === "") {
        errorBox.innerText = "El nombre de usuario no puede estar vacío";
        errorBox.classList.add("active");   // muestra recuadro
        usernameInput.classList.add("invalid");
    } else if (!regex.test(nombre)) {
        errorBox.innerText = "El nombre de usuario solo puede contener letras y números";
        errorBox.classList.add("active");
        usernameInput.classList.add("invalid");
    } else {
        errorBox.innerText = "";
        errorBox.classList.remove("active"); // oculta recuadro
        usernameInput.classList.remove("invalid");
    }
}

function validarPassword() {
    passwordInput = document.getElementById("password");
    password = passwordInput.value;
    errorBox = document.getElementById("passwordError");

    if (password === "") {
        errorBox.innerText = "La contraseña no puede estar vacía";
        errorBox.classList.add("active");   // muestra recuadro
        passwordInput.classList.add("invalid");
    } else {
        errorBox.innerText = "";
        errorBox.classList.remove("active"); // oculta recuadro
        passwordInput.classList.remove("invalid");
    }
}
