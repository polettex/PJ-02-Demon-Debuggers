document.addEventListener("DOMContentLoaded", () => {
    const registerForm = document.getElementById("registerForm");
  
    // Asignar eventos de validación a los campos
    document.getElementById("name").onblur = validarNombre;
    document.getElementById("apellido").onblur = validarApellido;
    document.getElementById("username").onblur = validarUsuario;
    document.getElementById("password").onblur = validarPassword;
    document.getElementById("confirm_password").onblur = validarConfirmPassword;
  
    registerForm.onsubmit = function (event) {
      // Ejecutar todas las validaciones
      validarNombre();
      validarApellido();
      validarUsuario();
      validarPassword();
      validarConfirmPassword();
  
      // Verificar si hay errores
      nombreError = document.getElementById("nameError").innerText;
      apellidoError = document.getElementById("apellidoError").innerText;
      usernameError = document.getElementById("usernameError").innerText;
      passwordError = document.getElementById("passwordError").innerText;
      confirmPasswordError = document.getElementById("confirmPasswordError").innerText;
  
      if (
        nombreError !== "" ||
        apellidoError !== "" ||
        usernameError !== "" ||
        passwordError !== "" ||
        confirmPasswordError !== ""
      ) {
        errorBox = document.getElementById("clientErrorLogin");
        event.preventDefault(); // Bloquear el envío del formulario
        errorBox.classList.add("active"); // Mostrar el recuadro de error
        errorBox.innerText = "Por favor corrige los errores antes de continuar.";
      }
    };
  
    // Funciones de validación
    function validarNombre() {
      nameInput = document.getElementById("name");
      nombre = nameInput.value.trim();
      regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/; // Solo letras y espacios
      errorBox = document.getElementById("nameError");
  
      if (nombre === "") {
        errorBox.innerText = "El nombre no puede estar vacío";
        errorBox.classList.add("active");
        nameInput.classList.add("invalid");
      } else if (!regex.test(nombre)) {
        errorBox.innerText = "El nombre solo puede contener letras y espacios";
        errorBox.classList.add("active");
        nameInput.classList.add("invalid");
      } else {
        errorBox.innerText = "";
        errorBox.classList.remove("active");
        nameInput.classList.remove("invalid");
      }
    }
  
    function validarApellido() {
      apellidoInput = document.getElementById("apellido");
      apellido = apellidoInput.value.trim();
      regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/; // Solo letras y espacios
      errorBox = document.getElementById("apellidoError");
  
      if (apellido === "") {
        errorBox.innerText = "El apellido no puede estar vacío";
        errorBox.classList.add("active");
        apellidoInput.classList.add("invalid");
      } else if (!regex.test(apellido)) {
        errorBox.innerText = "El apellido solo puede contener letras y espacios";
        errorBox.classList.add("active");
        apellidoInput.classList.add("invalid");
      } else {
        errorBox.innerText = "";
        errorBox.classList.remove("active");
        apellidoInput.classList.remove("invalid");
      }
    }
  
    function validarUsuario() {
      usernameInput = document.getElementById("username");
      username = usernameInput.value.trim();
      regex = /^[a-zA-Z0-9]+$/; // Letras y números
      errorBox = document.getElementById("usernameError");
  
      if (username === "") {
        errorBox.innerText = "El nombre de usuario no puede estar vacío";
        errorBox.classList.add("active");
        usernameInput.classList.add("invalid");
      } else if (!regex.test(username)) {
        errorBox.innerText = "El nombre de usuario solo puede contener letras y números";
        errorBox.classList.add("active");
        usernameInput.classList.add("invalid");
      } else {
        errorBox.innerText = "";
        errorBox.classList.remove("active");
        usernameInput.classList.remove("invalid");
      }
    }
  
    function validarPassword() {
      passwordInput = document.getElementById("password");
      password = passwordInput.value.trim();
      errorBox = document.getElementById("passwordError");
  
      if (password === "") {
        errorBox.innerText = "La contraseña no puede estar vacía";
        errorBox.classList.add("active");
        passwordInput.classList.add("invalid");
      } else if (password.length < 6) {
        errorBox.innerText = "La contraseña debe tener al menos 6 caracteres";
        errorBox.classList.add("active");
        passwordInput.classList.add("invalid");
      } else {
        errorBox.innerText = "";
        errorBox.classList.remove("active");
        passwordInput.classList.remove("invalid");
      }
    }
  
    function validarConfirmPassword() {
      passwordInput = document.getElementById("password");
      confirmPasswordInput = document.getElementById("confirm_password");
      confirmPassword = confirmPasswordInput.value.trim();
      errorBox = document.getElementById("confirmPasswordError");
  
      if (confirmPassword === "") {
        errorBox.innerText = "La confirmación de contraseña no puede estar vacía";
        errorBox.classList.add("active");
        confirmPasswordInput.classList.add("invalid");
      } else {
        errorBox.innerText = "";
        errorBox.classList.remove("active");
        confirmPasswordInput.classList.remove("invalid");
      }
    }
  });