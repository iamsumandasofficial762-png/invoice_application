(function () {
    const input = document.getElementById('customerLiveSearch');
    const tableBody = document.getElementById('customerTableBody');
    const countText = document.getElementById('customerCount');
    const emptyState = document.getElementById('customerEmptyState');
    const pagination = document.getElementById('customerPagination');
    const perPageSelect = document.querySelector('[data-per-page-select]');
    const searchUrl = document.querySelector('[data-customer-search-url]')?.dataset.customerSearchUrl;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    let debounceId;

    if (!input || !tableBody || !countText || !emptyState || !searchUrl) {
        return;
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function customerCountLabel(count) {
        return 'Showing ' + count + ' ' + (count === 1 ? 'customer' : 'customers');
    }

    function renderActions(customer) {
        return [
            '<div class="customer-actions">',
            '<a class="customer-action customer-action-view icon-btn icon-btn-primary" href="' + escapeHtml(customer.show_url) + '" title="View customer" aria-label="View customer"><i class="far fa-eye"></i></a>',
            '<a class="customer-action customer-action-edit icon-btn icon-btn-warning" href="' + escapeHtml(customer.edit_url) + '" title="Edit customer" aria-label="Edit customer"><i class="fas fa-pen"></i></a>',
            '<form method="POST" action="' + escapeHtml(customer.delete_url) + '" data-confirm="Are you sure you want to delete this customer?">',
            '<input type="hidden" name="_token" value="' + escapeHtml(csrfToken) + '">',
            '<input type="hidden" name="_method" value="DELETE">',
            '<button class="customer-action customer-action-delete icon-btn icon-btn-danger" type="submit" title="Delete customer" aria-label="Delete customer"><i class="fas fa-trash"></i></button>',
            '</form>',
            '</div>',
        ].join('');
    }

    function renderCustomers(customers) {
        tableBody.innerHTML = customers.map(function (customer) {
            return [
                '<tr>',
                '<td data-label="Name">' + escapeHtml(customer.name) + '</td>',
                '<td data-label="GST"><span class="gst-badge">' + escapeHtml(customer.gst) + '</span></td>',
                '<td data-label="State">' + escapeHtml(customer.state) + '</td>',
                '<td data-label="Phone">' + escapeHtml(customer.phone || '-') + '</td>',
                '<td data-label="Actions">' + renderActions(customer) + '</td>',
                '</tr>',
            ].join('');
        }).join('');
    }

    function fetchCustomers(search) {
        const url = new URL(searchUrl, window.location.origin);
        url.searchParams.set('search', search);
        url.searchParams.set('per_page', perPageSelect?.value || '10');

        fetch(url.toString(), {
            headers: {
                'Accept': 'application/json',
            },
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Customer search failed.');
                }

                return response.json();
            })
            .then(function (data) {
                const customers = data.customers || [];
                renderCustomers(customers);
                countText.textContent = customerCountLabel(data.count || 0);
                emptyState.classList.toggle('is-visible', customers.length === 0);

                if (pagination) {
                    pagination.innerHTML = '';
                }
            })
            .catch(function () {
                renderCustomers([]);
                countText.textContent = customerCountLabel(0);
                emptyState.textContent = 'Unable to load customers. Please try again.';
                emptyState.classList.add('is-visible');
            });
    }

    input.addEventListener('input', function () {
        const search = input.value.trim();

        window.clearTimeout(debounceId);

        if (search.length === 1) {
            return;
        }

        debounceId = window.setTimeout(function () {
            fetchCustomers(search);
        }, 300);
    });

    perPageSelect?.addEventListener('change', function () {
        perPageSelect.form?.submit();
    });
})();
