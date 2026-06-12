document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-password-toggle]').forEach(function (toggle) {
        const targetId = toggle.dataset.passwordTarget;
        const input = document.getElementById(targetId);

        if (!input) {
            return;
        }

        toggle.addEventListener('click', function () {
            const isHidden = input.type === 'password';
            const icon = toggle.querySelector('i');

            input.type = isHidden ? 'text' : 'password';
            toggle.setAttribute('aria-label', (isHidden ? 'Hide ' : 'Show ') + input.name.replaceAll('_', ' '));

            if (icon) {
                icon.classList.toggle('fa-eye', !isHidden);
                icon.classList.toggle('fa-eye-slash', isHidden);
            }
        });
    });
});
