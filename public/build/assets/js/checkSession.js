// resources/js/checkSession.js
document.addEventListener("DOMContentLoaded", function() {
    let form = document.querySelector("#updateUserForm");

    if (form) {
        form.addEventListener("submit", function(event) {
            event.preventDefault();

            // Realizamos una solicitud al servidor para comprobar si la sesión sigue activa
            fetch("/session/check", {
                method: "GET",
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            }).then(response => {
                if (response.status === 401) { // Sesión expirada
                    alert('Tu sesión ha expirado, por favor inicia sesión de nuevo.');
                    window.location.href = "/login";
                } else {
                    form.submit(); // Si la sesión sigue activa, envía el formulario
                }
            }).catch(error => {
                console.error("Error verificando la sesión:", error);
            });
        });
    }
});

