(function () {
    const select = document.getElementById('customerSelect2');
    const preview = document.getElementById('buyerDetailsPreview');

    if (!select || !preview || !window.jQuery || !jQuery.fn.select2) {
        return;
    }

    const $select = jQuery(select);

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

    function selectCustomer(customer) {
        if (!customer || !customer.id) {
            $select.val('').trigger('change');
            renderPreview(null);
            return;
        }

        let option = select.querySelector('option[value="' + customer.id + '"]');

        if (!option) {
            option = new Option(customer.name + ' - ' + customer.gst, customer.id, true, true);
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

        $select.trigger('change');
        renderPreview(customer);
    }

    $select.select2({
        ajax: {
            delay: 300,
            data: function (params) {
                return {
                    term: params.term || '',
                };
            },
            processResults: function (data) {
                return {
                    results: data.results || [],
                };
            },
            url: select.dataset.select2Url,
        },
        minimumInputLength: 0,
        placeholder: 'Select customer or search by name, GST, state, phone, or email',
        width: '100%',
    });

    $select.on('select2:select', function (event) {
        renderPreview(event.params.data.customer);
    });

    $select.on('select2:clear change', function () {
        if (!select.value) {
            renderPreview(null);
        }
    });

    window.selectInvoiceCustomer = selectCustomer;
    renderPreview(customerFromSelectedOption());
})();
