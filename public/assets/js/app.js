(function () {
    const modal = document.getElementById('deleteConfirmModal');
    const message = document.querySelector('[data-delete-confirm-message]');
    const confirmButton = document.querySelector('[data-delete-confirm]');
    const cancelButton = document.querySelector('[data-delete-cancel]');
    let pendingForm = null;

    if (!modal || !message || !confirmButton || !cancelButton) {
        return;
    }

    function openModal(form) {
        pendingForm = form;
        message.textContent = form.dataset.confirm || 'Are you sure you want to delete this record?';
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        confirmButton.focus();
    }

    function closeModal() {
        pendingForm = null;
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
    }

    document.addEventListener('submit', function (event) {
        const form = event.target;

        if (!form.matches('[data-confirm]') || form.dataset.confirmed === 'true') {
            return;
        }

        event.preventDefault();
        openModal(form);
    });

    confirmButton.addEventListener('click', function () {
        if (!pendingForm) {
            closeModal();
            return;
        }

        pendingForm.dataset.confirmed = 'true';
        pendingForm.submit();
    });

    cancelButton.addEventListener('click', closeModal);

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });
})();
