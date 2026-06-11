(function () {
    const input = document.getElementById('invoiceLiveSearch');
    const tableBody = document.getElementById('invoiceTableBody');
    const countText = document.getElementById('invoiceCount');
    const emptyState = document.getElementById('invoiceEmptyState');
    const pagination = document.getElementById('invoicePagination');
    const searchUrl = document.querySelector('[data-invoice-search-url]')?.dataset.invoiceSearchUrl;
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

    function invoiceCountLabel(count) {
        return 'Showing ' + count + ' ' + (count === 1 ? 'invoice' : 'invoices');
    }

    function renderActions(invoice) {
        return [
            '<div class="invoice-list-actions">',
            '<a class="invoice-list-action invoice-action-view" href="' + escapeHtml(invoice.show_url) + '">View</a>',
            '<a class="invoice-list-action invoice-action-edit" href="' + escapeHtml(invoice.edit_url) + '">Edit</a>',
            '<a class="invoice-list-action invoice-action-pdf" href="' + escapeHtml(invoice.pdf_url) + '">Download PDF</a>',
            '<a class="invoice-list-action invoice-action-print" href="' + escapeHtml(invoice.print_url) + '">Print</a>',
            '<form method="POST" action="' + escapeHtml(invoice.delete_url) + '" data-confirm="Are you sure you want to delete this invoice?">',
            '<input type="hidden" name="_token" value="' + escapeHtml(csrfToken) + '">',
            '<input type="hidden" name="_method" value="DELETE">',
            '<button class="invoice-list-action invoice-action-delete" type="submit">Delete</button>',
            '</form>',
            '</div>',
        ].join('');
    }

    function renderInvoices(invoices) {
        tableBody.innerHTML = invoices.map(function (invoice) {
            const status = invoice.payment_status || 'unpaid';

            return [
                '<tr>',
                '<td>' + escapeHtml(invoice.invoice_number) + '</td>',
                '<td>' + escapeHtml(invoice.invoice_date) + '</td>',
                '<td>' + escapeHtml(invoice.customer_name) + '</td>',
                '<td><span class="gst-badge">' + escapeHtml(invoice.customer_gst) + '</span></td>',
                '<td>Rs. ' + escapeHtml(invoice.net_payable_amount) + '</td>',
                '<td><span class="status-badge status-' + escapeHtml(status) + '">' + escapeHtml(status.charAt(0).toUpperCase() + status.slice(1)) + '</span></td>',
                '<td>' + renderActions(invoice) + '</td>',
                '</tr>',
            ].join('');
        }).join('');
    }

    function fetchInvoices(search) {
        const url = new URL(searchUrl, window.location.origin);
        url.searchParams.set('search', search);

        fetch(url.toString(), {
            headers: {
                'Accept': 'application/json',
            },
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Invoice search failed.');
                }

                return response.json();
            })
            .then(function (data) {
                const invoices = data.invoices || [];
                renderInvoices(invoices);
                countText.textContent = invoiceCountLabel(data.count || 0);
                emptyState.classList.toggle('is-visible', invoices.length === 0);

                if (pagination) {
                    pagination.innerHTML = '';
                }
            })
            .catch(function () {
                renderInvoices([]);
                countText.textContent = invoiceCountLabel(0);
                emptyState.textContent = 'Unable to load invoices. Please try again.';
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
            fetchInvoices(search);
        }, 300);
    });
})();
