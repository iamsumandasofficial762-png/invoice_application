document.addEventListener('submit', function (event) {
    const form = event.target;

    if (!form.matches('[data-confirm]')) {
        return;
    }

    if (!window.confirm(form.dataset.confirm)) {
        event.preventDefault();
    }
});
