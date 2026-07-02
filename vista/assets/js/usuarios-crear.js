(function () {

  const paleta   = ['primary', 'success', 'danger', 'warning', 'info'];
  const hashStr  = s => Math.abs([...s].reduce((a, c) => (Math.imul(31, a) + c.charCodeAt(0)) | 0, 0));
  const avatar   = document.getElementById('avatarPreview');
  const nombreIn = document.getElementById('nombre');

  nombreIn.addEventListener('input', () => {
    const v = nombreIn.value.trim();
    if (v) {
      const color = paleta[hashStr(v) % paleta.length];
      avatar.style.cssText = 'width:42px;height:42px;font-size:1rem;flex-shrink:0;';
      avatar.className = `rounded-circle bg-${color} text-white d-flex align-items-center justify-content-center fw-semibold`;
      avatar.textContent = v[0].toUpperCase();
    } else {
      avatar.style.cssText = 'width:42px;height:42px;font-size:1rem;flex-shrink:0;';
      avatar.className = 'rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center fw-semibold';
      avatar.innerHTML = '<i class="bi bi-person"></i>';
    }
  });

  document.querySelectorAll('.toggle-pwd').forEach(btn => {
    btn.addEventListener('click', () => {
      const inp  = document.getElementById(btn.dataset.target);
      const icon = btn.querySelector('i');
      const show = inp.type === 'password';
      inp.type       = show ? 'text' : 'password';
      icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
  });

  const pwd1 = document.getElementById('contrasena');
  const pwd2 = document.getElementById('confirmar');
  const err  = document.getElementById('confirmarError');

  function checkMatch() {
    const mismatch = pwd2.value && pwd1.value !== pwd2.value;
    pwd2.classList.toggle('is-invalid', mismatch);
    err.style.display = mismatch ? 'block' : 'none';
  }
  pwd1.addEventListener('input', checkMatch);
  pwd2.addEventListener('input', checkMatch);

  document.getElementById('formCrear').addEventListener('submit', e => {
    if (pwd1.value !== pwd2.value) { e.preventDefault(); checkMatch(); }
  });

  document.querySelectorAll('input[name="rol"]').forEach(radio => {
    radio.addEventListener('change', () => {
      document.querySelectorAll('.rol-card').forEach(c => c.classList.remove('selected'));
      radio.closest('.rol-card').classList.add('selected');
    });
  });

})();
