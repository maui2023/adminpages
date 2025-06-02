</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const toggle = document.getElementById('themeToggle');
if (toggle) {
  toggle.addEventListener('click', () => {
    const html = document.documentElement;
    html.dataset.bsTheme = html.dataset.bsTheme === 'light' ? 'dark' : 'light';
  });
}
</script>
</body>
</html>
