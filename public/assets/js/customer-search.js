(function () {
    const input = document.getElementById('customerLiveSearch');
    const tableBody = document.getElementById('customerTableBody');
    const countText = document.getElementById('customerCount');
    const emptyState = document.getElementById('customerEmptyState');
    const pagination = document.getElementById('customerPagination');
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
            '<a class="customer-action customer-action-view" href="' + escapeHtml(customer.show_url) + '">View</a>',
            '<a class="customer-action customer-action-edit" href="' + escapeHtml(customer.edit_url) + '">Edit</a>',
            '<form method="POST" action="' + escapeHtml(customer.delete_url) + '" data-confirm="Are you sure you want to delete this customer?">',
            '<input type="hidden" name="_token" value="' + escapeHtml(csrfToken) + '">',
            '<input type="hidden" name="_method" value="DELETE">',
            '<button class="customer-action customer-action-delete" type="submit">Delete</button>',
            '</form>',
            '</div>',
        ].join('');
    }

    function renderCustomers(customers) {
        tableBody.innerHTML = customers.map(function (customer) {
            return [
                '<tr>',
                '<td>' + escapeHtml(customer.name) + '</td>',
                '<td><span class="gst-badge">' + escapeHtml(customer.gst) + '</span></td>',
                '<td>' + escapeHtml(customer.state) + '</td>',
                '<td>' + escapeHtml(customer.phone || '-') + '</td>',
                '<td>' + renderActions(customer) + '</td>',
                '</tr>',
            ].join('');
        }).join('');
    }

    function fetchCustomers(search) {
        const url = new URL(searchUrl, window.location.origin);
        url.searchParams.set('search', search);

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
})();
