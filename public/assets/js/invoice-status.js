(function () {
    const modal = document.getElementById('statusConfirmModal');
    const form = document.getElementById('statusConfirmForm');
    const valueInput = document.getElementById('statusConfirmValue');
    const statusLabel = document.querySelector('[data-status-label]');

    if (!modal || !form || !valueInput || !statusLabel) {
        return;
    }

    function openModal(status, action, statusText) {
        const actionType = (statusText || status || '').toLowerCase();

        form.action = action;
        valueInput.value = status;
        statusLabel.textContent = statusText || status;
        modal.classList.toggle('is-paid-action', actionType === 'paid');
        modal.classList.toggle('is-cancel-action', actionType === 'cancel' || actionType === 'unpaid');
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
    }

    function closeModal() {
        modal.classList.remove('is-open');
        modal.classList.remove('is-paid-action', 'is-cancel-action');
        modal.setAttribute('aria-hidden', 'true');
    }

    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('[data-status-trigger]');

        if (trigger) {
            openModal(trigger.dataset.status, trigger.dataset.action, trigger.dataset.statusText);
        }

        if (event.target.matches('[data-status-cancel]') || event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });
})();
