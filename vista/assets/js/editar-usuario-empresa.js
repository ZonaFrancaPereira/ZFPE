document.addEventListener('DOMContentLoaded', function () {

    // Toggle visibilidad de contraseña
    document.querySelectorAll('.btn-toggle-pass').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.getElementById(btn.dataset.target);
            var icon  = btn.querySelector('i');
            if (!input) return;
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        });
    });

    // Validar coincidencia de contraseñas antes de enviar
    var form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function (e) {
            var p1  = document.getElementById('contrasena');
            var p2  = document.getElementById('contrasena2');
            var err = document.getElementById('errorPass');
            if (p1 && p2 && p1.value && p1.value !== p2.value) {
                e.preventDefault();
                if (err) err.style.display = 'block';
                p2.classList.add('is-invalid');
            } else {
                if (err) err.style.display = 'none';
                if (p2) p2.classList.remove('is-invalid');
            }
        });
    }

});
