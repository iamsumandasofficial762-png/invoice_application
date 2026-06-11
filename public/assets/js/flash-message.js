document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-flash-message]').forEach(function (message) {
        let timeoutId;

        function hideMessage() {
            if (!message || message.classList.contains('is-hiding')) {
                return;
            }

            message.classList.add('is-hiding');
            window.setTimeout(function () {
                message.remove();
            }, 350);
        }

        timeoutId = window.setTimeout(hideMessage, 3000);

        message.querySelector('[data-flash-close]')?.addEventListener('click', function () {
            window.clearTimeout(timeoutId);
            hideMessage();
        });
    });
});
