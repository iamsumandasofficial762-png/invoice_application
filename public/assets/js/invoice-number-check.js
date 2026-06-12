(function () {
    const input = document.getElementById('invoiceNumberInput');
    const feedback = document.querySelector('[data-invoice-number-feedback]');

    if (!input || !feedback) {
        return;
    }

    let debounceTimer;
    let abortController;

    function clearFeedback() {
        feedback.textContent = '';
        feedback.classList.remove('is-warning');
    }

    function showDuplicate(data) {
        feedback.innerHTML = '';
        feedback.classList.add('is-warning');

        const message = document.createElement('span');
        message.textContent = data.message || 'This invoice number is already used.';
        feedback.appendChild(message);

        if (data.suggested) {
            const suggestion = document.createElement('button');
            suggestion.type = 'button';
            suggestion.textContent = 'Suggested invoice number: ' + data.suggested;
            suggestion.addEventListener('click', function () {
                input.value = data.suggested;
                clearFeedback();
            });
            feedback.appendChild(suggestion);
        }
    }

    function checkNumber() {
        const invoiceNumber = input.value.trim();

        if (abortController) {
            abortController.abort();
        }

        if (!invoiceNumber) {
            clearFeedback();
            return;
        }

        abortController = new AbortController();

        const params = new URLSearchParams({
            invoice_number: invoiceNumber,
        });

        if (input.dataset.invoiceId) {
            params.set('invoice_id', input.dataset.invoiceId);
        }

        fetch(input.dataset.checkUrl + '?' + params.toString(), {
            headers: {
                'Accept': 'application/json',
            },
            signal: abortController.signal,
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Unable to check invoice number');
                }

                return response.json();
            })
            .then(function (data) {
                if (data.exists) {
                    showDuplicate(data);
                    return;
                }

                clearFeedback();
            })
            .catch(function (error) {
                if (error.name !== 'AbortError') {
                    clearFeedback();
                }
            });
    }

    input.addEventListener('input', function () {
        window.clearTimeout(debounceTimer);
        debounceTimer = window.setTimeout(checkNumber, 400);
    });
})();
