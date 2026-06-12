(function () {
    const selector = document.querySelector('[data-customer-selector]');
    const searchInput = document.querySelector('[data-customer-search-input]');
    const customerIdInput = document.querySelector('[data-customer-id-input]');
    const resultsBox = document.querySelector('[data-customer-search-results]');
    const helper = document.querySelector('[data-customer-search-helper]');
    const preview = document.querySelector('[data-buyer-details-preview]');

    if (!selector || !searchInput || !customerIdInput || !resultsBox || !preview) {
        return;
    }

    const searchUrl = selector.dataset.searchUrl;
    let debounceTimer;
    let abortController;

    function setHelper(message) {
        if (helper) {
            helper.textContent = message;
        }
    }

    function clearResults() {
        resultsBox.innerHTML = '';
        resultsBox.classList.remove('is-open');
    }

    function createLine(text) {
        const span = document.createElement('span');
        span.textContent = text || '-';

        return span;
    }

    function renderPreview(customer) {
        preview.innerHTML = '';

        if (!customer || !customer.id) {
            preview.textContent = 'Select a customer to show details.';
            preview.classList.add('empty-state');
            return;
        }

        preview.classList.remove('empty-state');

        const name = document.createElement('strong');
        name.textContent = customer.name || '-';

        preview.append(
            name,
            createLine(customer.address),
            createLine((customer.state || '-') + ' - ' + (customer.pin || '-')),
            createLine('Phone: ' + (customer.phone || '-')),
            createLine('Gmail: ' + (customer.gmail || '-')),
            createLine('GSTIN: ' + (customer.gst || '-')),
        );
    }

    function selectCustomer(customer) {
        customerIdInput.value = customer.id || '';
        searchInput.value = customer.id ? (customer.name + ' - ' + customer.gst) : '';
        renderPreview(customer);
        clearResults();
        setHelper(customer.id ? 'Selected customer details are shown below.' : 'Type at least 2 characters to search customers.');
    }

    function renderMessage(message) {
        resultsBox.innerHTML = '';

        const item = document.createElement('div');
        item.className = 'customer-search-message';
        item.textContent = message;
        resultsBox.appendChild(item);
        resultsBox.classList.add('is-open');
    }

    function renderResults(customers) {
        resultsBox.innerHTML = '';

        if (!customers.length) {
            renderMessage('No customers found');
            return;
        }

        customers.forEach(function (customer) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'customer-search-result';

            const title = document.createElement('strong');
            title.textContent = customer.name || '-';

            const meta = document.createElement('span');
            meta.textContent = [
                'GST: ' + (customer.gst || '-'),
                'State: ' + (customer.state || '-'),
                'Phone: ' + (customer.phone || '-'),
            ].join(' | ');

            button.append(title, meta);
            button.addEventListener('click', function () {
                selectCustomer(customer);
            });
            resultsBox.appendChild(button);
        });

        resultsBox.classList.add('is-open');
    }

    function searchCustomers(query) {
        if (abortController) {
            abortController.abort();
        }

        abortController = new AbortController();
        renderMessage('Searching customers...');

        fetch(searchUrl + '?q=' + encodeURIComponent(query), {
            headers: {
                'Accept': 'application/json',
            },
            signal: abortController.signal,
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Search failed');
                }

                return response.json();
            })
            .then(function (data) {
                renderResults(data.customers || []);
            })
            .catch(function (error) {
                if (error.name === 'AbortError') {
                    return;
                }

                renderMessage('Unable to search customers');
            });
    }

    searchInput.addEventListener('input', function () {
        const query = searchInput.value.trim();
        customerIdInput.value = '';
        renderPreview(null);

        window.clearTimeout(debounceTimer);

        if (query.length < 2) {
            clearResults();
            setHelper('Type at least 2 characters to search customers.');
            return;
        }

        setHelper('Select a customer from the search results.');
        debounceTimer = window.setTimeout(function () {
            searchCustomers(query);
        }, 300);
    });

    searchInput.addEventListener('focus', function () {
        if (searchInput.value.trim().length < 2) {
            setHelper('Type at least 2 characters to search customers.');
        }
    });

    document.addEventListener('click', function (event) {
        if (!selector.contains(event.target)) {
            clearResults();
        }
    });

    window.selectInvoiceCustomer = selectCustomer;

    if (customerIdInput.value) {
        renderPreview({
            id: customerIdInput.value,
            name: preview.dataset.name,
            address: preview.dataset.address,
            state: preview.dataset.state,
            pin: preview.dataset.pin,
            phone: preview.dataset.phone,
            gmail: preview.dataset.gmail,
            gst: preview.dataset.gst,
        });
    } else {
        renderPreview(null);
    }
})();
