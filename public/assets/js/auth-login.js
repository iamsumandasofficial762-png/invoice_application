document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.querySelector('[data-password-toggle]');
    const passwordInput = document.getElementById('login-password');

    if (!toggle || !passwordInput) {
        return;
    }

    toggle.addEventListener('click', function () {
        const isHidden = passwordInput.type === 'password';
        const icon = toggle.querySelector('i');

        passwordInput.type = isHidden ? 'text' : 'password';
        toggle.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');

        if (icon) {
            icon.classList.toggle('fa-eye', !isHidden);
            icon.classList.toggle('fa-eye-slash', isHidden);
        }
    });
});
