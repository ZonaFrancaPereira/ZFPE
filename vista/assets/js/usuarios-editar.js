(function () {

  const paleta   = ['primary', 'success', 'danger', 'warning', 'info'];
  const hashStr  = s => Math.abs([...s].reduce((a, c) => (Math.imul(31, a) + c.charCodeAt(0)) | 0, 0));
  const avatar   = document.getElementById('avatarPreview');
  const nombreIn = document.getElementById('nombre');
  const heading  = avatar.nextElementSibling.querySelector('h3');

  nombreIn.addEventListener('input', () => {
    const v = nombreIn.value.trim();
    if (v) {
      const color = paleta[hashStr(v) % paleta.length];
      avatar.className = `rounded-circle bg-${color} text-white d-flex align-items-center justify-content-center fw-semibold flex-shrink-0`;
      avatar.textContent = v[0].toUpperCase();
      heading.textContent = v;
    } else {
      avatar.className = 'rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center fw-semibold flex-shrink-0';
      avatar.innerHTML = '<i class="bi bi-person"></i>';
      heading.textContent = '';
    }
  });

  document.querySelectorAll('input[name="rol"]').forEach(radio => {
    radio.addEventListener('change', () => {
      document.querySelectorAll('.rol-card').forEach(c => c.classList.remove('selected'));
      radio.closest('.rol-card').classList.add('selected');
    });
  });

})();
