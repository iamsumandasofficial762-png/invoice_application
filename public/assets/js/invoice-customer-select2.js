(function () {
    const select = document.getElementById('customerSelect2');
    const preview = document.getElementById('buyerDetailsPreview');

    if (!select || !preview) {
        return;
    }

    let hiddenInput = null;
    let searchInput = null;
    let resultsBox = null;
    let debounceTimer = null;
    let activeRequest = null;

    function createLine(text) {
        const span = document.createElement('span');
        span.textContent = text || '-';

        return span;
    }

    function renderPreview(customer) {
        preview.innerHTML = '';
        preview.dataset.state = customer?.state || '';

        if (!customer || !customer.id) {
            preview.textContent = 'Select a customer to show details.';
            preview.classList.add('empty-state');
            return;
        }

        preview.classList.remove('empty-state');

        const name = document.createElement('strong');
        name.textContent = customer.name || '-';

        const lines = [
            name,
            createLine(customer.address),
            createLine((customer.state || '-') + ' - ' + (customer.pin || '-')),
            createLine('Phone: ' + (customer.phone || '-')),
        ];

        if (customer.gmail) {
            lines.push(createLine('Email: ' + customer.gmail));
        }

        lines.push(createLine('GSTIN: ' + (customer.gst || '-')));

        preview.append(...lines);
    }

    function customerFromSelectedOption() {
        const option = select.options[select.selectedIndex];

        if (!option || !option.value) {
            return null;
        }

        return {
            id: option.value,
            name: option.dataset.name,
            address: option.dataset.address,
            state: option.dataset.state,
            pin: option.dataset.pin,
            phone: option.dataset.phone,
            gmail: option.dataset.gmail,
            gst: option.dataset.gst,
        };
    }

    function normalizeCustomer(data) {
        return data?.customer || data;
    }

    function selectedLabel(customer) {
        if (!customer || !customer.id) {
            return '';
        }

        return (customer.name || '') + (customer.gst ? ' - ' + customer.gst : '');
    }

    function ensureSelectedOption(customer) {
        let option = select.querySelector('option[value="' + customer.id + '"]');

        if (!option) {
            option = new Option(selectedLabel(customer), customer.id, true, true);
            select.appendChild(option);
        }

        option.dataset.name = customer.name || '';
        option.dataset.address = customer.address || '';
        option.dataset.state = customer.state || '';
        option.dataset.pin = customer.pin || '';
        option.dataset.phone = customer.phone || '';
        option.dataset.gmail = customer.gmail || '';
        option.dataset.gst = customer.gst || '';
        option.selected = true;
    }

    function closeResults() {
        if (resultsBox) {
            resultsBox.classList.remove('is-open');
        }
    }

    function setMessage(message) {
        resultsBox.innerHTML = '<div class="customer-search-message">' + message + '</div>';
        resultsBox.classList.add('is-open');
    }

    function selectCustomer(data) {
        const customer = normalizeCustomer(data);

        if (!customer || !customer.id) {
            hiddenInput.value = '';
            searchInput.value = '';
            select.value = '';
            renderPreview(null);
            document.dispatchEvent(new CustomEvent('invoice:customer-selected', {
                detail: {
                    customer: null,
                },
            }));
            closeResults();
            return;
        }

        ensureSelectedOption(customer);
        hiddenInput.value = customer.id;
        searchInput.value = selectedLabel(customer);
        renderPreview(customer);
        document.dispatchEvent(new CustomEvent('invoice:customer-selected', {
            detail: {
                customer: customer,
            },
        }));
        closeResults();
    }

    function renderResults(results) {
        resultsBox.innerHTML = '';

        if (!results.length) {
            setMessage('No customers found.');
            return;
        }

        results.forEach(function (result) {
            const customer = normalizeCustomer(result);
            const button = document.createElement('button');
            const name = document.createElement('strong');
            const detail = document.createElement('span');
            const meta = [customer.gst, customer.state, customer.phone].filter(Boolean).join(' | ');

            button.type = 'button';
            button.className = 'customer-search-result';
            name.textContent = customer.name || '-';
            detail.textContent = meta;
            button.append(name, detail);
            button.addEventListener('click', function () {
                selectCustomer(customer);
            });

            resultsBox.appendChild(button);
        });

        resultsBox.classList.add('is-open');
    }

    function searchCustomers(term) {
        const url = new URL(select.dataset.select2Url, window.location.origin);
        url.searchParams.set('term', term || '');

        if (activeRequest) {
            activeRequest.abort();
        }

        activeRequest = new AbortController();
        setMessage('Searching customers...');

        fetch(url, {
            headers: {
                Accept: 'application/json',
            },
            signal: activeRequest.signal,
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Search failed.');
                }

                return response.json();
            })
            .then(function (data) {
                renderResults(data.results || []);
            })
            .catch(function (error) {
                if (error.name !== 'AbortError') {
                    setMessage('Unable to search customers.');
                }
            });
    }

    function queueSearch() {
        window.clearTimeout(debounceTimer);
        debounceTimer = window.setTimeout(function () {
            searchCustomers(searchInput.value.trim());
        }, 250);
    }

    function initNativeAutocomplete() {
        const initialCustomer = customerFromSelectedOption();
        const wrapper = document.createElement('div');

        hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = select.name;
        hiddenInput.value = select.value || '';

        searchInput = document.createElement('input');
        searchInput.type = 'search';
        searchInput.className = 'customer-search-input';
        searchInput.placeholder = 'Select customer or search by name, GST, state, phone, or email';
        searchInput.autocomplete = 'off';
        searchInput.value = selectedLabel(initialCustomer);

        resultsBox = document.createElement('div');
        resultsBox.className = 'customer-search-results';

        wrapper.className = 'customer-autocomplete';
        wrapper.append(searchInput, resultsBox);

        select.name = '';
        select.required = false;
        select.classList.add('is-native-autocomplete');
        select.insertAdjacentElement('beforebegin', hiddenInput);
        select.insertAdjacentElement('afterend', wrapper);

        searchInput.addEventListener('focus', function () {
            searchCustomers(searchInput.value.trim());
        });

        searchInput.addEventListener('input', function () {
            hiddenInput.value = '';
            select.value = '';
            renderPreview(null);
            document.dispatchEvent(new CustomEvent('invoice:customer-selected', {
                detail: {
                    customer: null,
                },
            }));
            queueSearch();
        });

        document.addEventListener('click', function (event) {
            if (!wrapper.contains(event.target) && event.target !== searchInput) {
                closeResults();
            }
        });

        window.selectInvoiceCustomer = selectCustomer;
        renderPreview(initialCustomer);
        document.dispatchEvent(new CustomEvent('invoice:customer-selected', {
            detail: {
                customer: initialCustomer,
            },
        }));
    }

    initNativeAutocomplete();
})();
