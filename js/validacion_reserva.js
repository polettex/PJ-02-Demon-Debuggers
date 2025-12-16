document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formBuscar');
    if (form) {
        form.addEventListener('submit', function(e) {
            const fechaInput = document.getElementById('fechaReserva').value;
            // Obtener fecha de hoy en formato YYYY-MM-DD local
            const hoy = new Date();
            const year = hoy.getFullYear();
            const month = String(hoy.getMonth() + 1).padStart(2, '0');
            const day = String(hoy.getDate()).padStart(2, '0');
            const hoyString = `${year}-${month}-${day}`;

            if (fechaInput < hoyString) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Fecha inválida',
                    text: 'No puedes reservar en una fecha anterior a hoy.',
                    confirmButtonColor: '#d33'
                });
            }
        });
    }
});
