(function () {

  document.querySelectorAll('.toggle-pwd').forEach(btn => {
    btn.addEventListener('click', () => {
      const inp  = document.getElementById(btn.dataset.target);
      const icon = btn.querySelector('i');
      const show = inp.type === 'password';
      inp.type       = show ? 'text' : 'password';
      icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
  });

  const pwd1 = document.getElementById('nueva_contrasena');
  const pwd2 = document.getElementById('confirmar_contrasena');
  const err  = document.getElementById('confirmarError');

  function checkMatch() {
    const mismatch = pwd2.value && pwd1.value !== pwd2.value;
    pwd2.classList.toggle('is-invalid', mismatch);
    err.style.display = mismatch ? 'block' : 'none';
  }
  pwd1.addEventListener('input', checkMatch);
  pwd2.addEventListener('input', checkMatch);

  document.getElementById('formPerfil').addEventListener('submit', e => {
    if (pwd1.value !== pwd2.value) { e.preventDefault(); checkMatch(); }
  });

})();
