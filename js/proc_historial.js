// --- Cuando cambia la sala ---
function manejarCambioSala() {
    var salaSelect = document.getElementById("sala");
    var mesaSelect = document.getElementById("mesa");

    if (mesaSelect) {
        mesaSelect.value = ""; // Limpia la mesa seleccionada
    }

    // Envía el formulario para recargar opciones
    if (salaSelect && salaSelect.form) {
        salaSelect.form.submit();
    }
}

// --- Cuando se hace clic en "Borrar filtros" ---
function borrarFiltros() {
    var form = document.querySelector(".filtro-form");

    if (form) {
        var campos = form.querySelectorAll("select, input");
        campos.forEach(function(campo) {
            campo.value = "";
        });

        form.submit();
    }
}

// --- Asociar eventos ---
function inicializarHistorial() {
    var salaSelect = document.getElementById("sala");
    var resetBtn = document.getElementById("btnReset");
    
    if (salaSelect) {
        // salaSelect.addEventListener("change", manejarCambioSala);
        salaSelect.onchange = manejarCambioSala;
    }

    if (resetBtn) {
        resetBtn.onclick = borrarFiltros;
    }
}

// --- Ejecutar al cargar completamente la página ---
window.onload = inicializarHistorial;