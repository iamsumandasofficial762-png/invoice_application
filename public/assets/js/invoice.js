(function () {
    const customerSelect = document.getElementById('customer_id');
    const customerDetails = document.getElementById('selected-customer-details');
    const itemsBody = document.querySelector('[data-items-body]');
    const rowTemplate = document.getElementById('item-row-template');
    const addItemButton = document.querySelector('[data-add-item]');
    const ajaxCustomerForm = document.getElementById('ajax-customer-form');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function money(value) {
        return Number(value || 0).toFixed(2);
    }

    function refreshCustomerDetails() {
        if (!customerSelect || !customerDetails) {
            return;
        }

        const selected = customerSelect.options[customerSelect.selectedIndex];

        if (!selected || !selected.value) {
            customerDetails.textContent = 'Select a customer to show details.';
            customerDetails.classList.add('empty-state');
            return;
        }

        customerDetails.classList.remove('empty-state');
        customerDetails.innerHTML = [
            '<strong>' + selected.dataset.name + '</strong>',
            '<span>' + selected.dataset.address + '</span>',
            '<span>' + selected.dataset.state + ' - ' + selected.dataset.pin + '</span>',
            '<span>Phone: ' + (selected.dataset.phone || '-') + '</span>',
            '<span>Gmail: ' + (selected.dataset.gmail || '-') + '</span>',
            '<span>GSTIN: ' + selected.dataset.gst + '</span>',
        ].join('');
    }

    function refreshRows() {
        if (!itemsBody) {
            return;
        }

        itemsBody.querySelectorAll('[data-item-row]').forEach(function (row, index) {
            row.querySelector('.sr-no').textContent = index + 1;
            row.querySelectorAll('input, textarea').forEach(function (field) {
                field.name = field.name.replace(/items\[[^\]]+\]/, 'items[' + index + ']');
            });

            const removeButton = row.querySelector('[data-remove-item]');
            removeButton.disabled = itemsBody.querySelectorAll('[data-item-row]').length === 1;
        });
    }

    function recalculate() {
        let subtotal = 0;

        document.querySelectorAll('[data-item-row]').forEach(function (row) {
            const unitPrice = parseFloat(row.querySelector('[data-unit-price]').value || '0');
            const amount = unitPrice;
            subtotal += amount;
            row.querySelector('[data-amount]').value = money(amount);
        });

        const cgst = subtotal * 0.09;
        const sgst = subtotal * 0.09;
        const totalTax = cgst + sgst;
        const gross = subtotal + totalTax;

        const values = {
            '[data-summary-subtotal]': subtotal,
            '[data-summary-cgst]': cgst,
            '[data-summary-sgst]': sgst,
            '[data-summary-total-tax]': totalTax,
            '[data-summary-gross]': gross,
            '[data-summary-net]': gross,
        };

        Object.keys(values).forEach(function (selector) {
            const element = document.querySelector(selector);

            if (element) {
                element.textContent = money(values[selector]);
            }
        });
    }

    function addItemRow() {
        const index = itemsBody.querySelectorAll('[data-item-row]').length;
        const html = rowTemplate.innerHTML.replaceAll('__INDEX__', index);
        itemsBody.insertAdjacentHTML('beforeend', html);
        refreshRows();
        recalculate();
    }

    function openModal(id) {
        document.getElementById(id)?.classList.add('is-open');
    }

    function closeModal(id) {
        document.getElementById(id)?.classList.remove('is-open');
    }

    function addCustomerOption(customer) {
        const option = document.createElement('option');
        option.value = customer.id;
        option.textContent = customer.name + ' - ' + customer.gst;
        option.dataset.name = customer.name;
        option.dataset.address = customer.address;
        option.dataset.state = customer.state;
        option.dataset.pin = customer.pin;
        option.dataset.phone = customer.phone || '';
        option.dataset.gmail = customer.gmail || '';
        option.dataset.gst = customer.gst;
        option.selected = true;
        customerSelect.appendChild(option);
        refreshCustomerDetails();
    }

    customerSelect?.addEventListener('change', refreshCustomerDetails);
    addItemButton?.addEventListener('click', addItemRow);

    itemsBody?.addEventListener('input', function (event) {
        if (event.target.matches('[data-unit-price]')) {
            recalculate();
        }
    });

    itemsBody?.addEventListener('click', function (event) {
        if (!event.target.matches('[data-remove-item]')) {
            return;
        }

        event.target.closest('[data-item-row]').remove();
        refreshRows();
        recalculate();
    });

    document.addEventListener('click', function (event) {
        const openButton = event.target.closest('[data-modal-open]');
        const closeButton = event.target.closest('[data-modal-close]');

        if (openButton) {
            openModal(openButton.dataset.modalOpen);
        }

        if (closeButton) {
            closeModal(closeButton.dataset.modalClose);
        }
    });

    ajaxCustomerForm?.addEventListener('submit', function (event) {
        event.preventDefault();

        const errorBox = ajaxCustomerForm.querySelector('[data-modal-errors]');
        errorBox.textContent = '';

        fetch(ajaxCustomerForm.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: new FormData(ajaxCustomerForm),
        })
            .then(function (response) {
                if (!response.ok) {
                    return response.json().then(function (data) {
                        throw data;
                    });
                }

                return response.json();
            })
            .then(function (data) {
                addCustomerOption(data.customer);
                ajaxCustomerForm.reset();
                closeModal('customer-modal');
            })
            .catch(function (error) {
                const errors = error.errors || {};
                const messages = Object.keys(errors).map(function (key) {
                    return errors[key].join(' ');
                });
                errorBox.textContent = messages.join(' ') || 'Unable to create customer.';
            });
    });

    refreshCustomerDetails();
    refreshRows();
    recalculate();
})();
