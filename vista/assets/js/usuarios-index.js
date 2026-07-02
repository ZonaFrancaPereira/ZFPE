document.getElementById('buscarUsuario')?.addEventListener('input', function () {
  const q = this.value.toLowerCase();
  document.querySelectorAll('#tablaUsuarios tbody tr').forEach(tr => {
    tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
});
