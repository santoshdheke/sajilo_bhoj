<script>
    // Show / hide a password field: <button data-toggle-pwd="inputId">.
    document.querySelectorAll('[data-toggle-pwd]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var p = document.getElementById(btn.dataset.togglePwd), show = p.type === 'password';
            p.type = show ? 'text' : 'password';
            btn.innerHTML = show ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
            btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        });
    });
</script>
