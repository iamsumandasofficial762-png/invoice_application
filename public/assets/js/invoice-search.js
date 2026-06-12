(function () {
    const input = document.getElementById('invoiceLiveSearch');
    const tableBody = document.getElementById('invoiceTableBody');
    const mobileCards = document.getElementById('invoiceMobileCards');
    const countText = document.getElementById('invoiceCount');
    const emptyState = document.getElementById('invoiceEmptyState');
    const pagination = document.getElementById('invoicePagination');
    const perPageSelect = document.querySelector('[data-per-page-select]');
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
            '<a class="invoice-list-action invoice-action-view icon-btn icon-btn-primary" href="' + escapeHtml(invoice.show_url) + '" title="View invoice" aria-label="View invoice"><i class="far fa-eye"></i></a>',
            '<a class="invoice-list-action invoice-action-edit icon-btn icon-btn-warning" href="' + escapeHtml(invoice.edit_url) + '" title="Edit invoice" aria-label="Edit invoice"><i class="fas fa-pen"></i></a>',
            '<a class="invoice-list-action invoice-action-pdf icon-btn icon-btn-info" href="' + escapeHtml(invoice.pdf_url) + '" title="Download PDF" aria-label="Download PDF"><i class="far fa-file-pdf"></i></a>',
            '<a class="invoice-list-action invoice-action-print icon-btn icon-btn-primary" href="' + escapeHtml(invoice.print_url) + '" title="Print invoice" aria-label="Print invoice"><i class="fas fa-print"></i></a>',
            '<button class="invoice-list-action invoice-action-share icon-btn icon-btn-success invoice-native-share" type="button" data-invoice-number="' + escapeHtml(invoice.invoice_number) + '" data-customer-name="' + escapeHtml(invoice.customer_name) + '" data-net-payable="' + escapeHtml(invoice.net_payable_amount) + '" data-pdf-url="' + escapeHtml(invoice.pdf_url) + '" title="Share invoice" aria-label="Share invoice"><i class="fas fa-share-alt"></i></button>',
            '<form method="POST" action="' + escapeHtml(invoice.delete_url) + '" data-confirm="Are you sure you want to delete this invoice?">',
            '<input type="hidden" name="_token" value="' + escapeHtml(csrfToken) + '">',
            '<input type="hidden" name="_method" value="DELETE">',
            '<button class="invoice-list-action invoice-action-delete icon-btn icon-btn-danger" type="submit" title="Delete invoice" aria-label="Delete invoice"><i class="fas fa-trash"></i></button>',
            '</form>',
            '</div>',
        ].join('');
    }

    function renderMobileActions(invoice) {
        return [
            '<div class="invoice-mobile-actions">',
            '<a class="invoice-mobile-action invoice-mobile-action-view icon-btn icon-btn-primary" href="' + escapeHtml(invoice.show_url) + '" aria-label="View invoice" title="View invoice"><i class="far fa-eye"></i></a>',
            '<a class="invoice-mobile-action invoice-mobile-action-pdf icon-btn icon-btn-info" href="' + escapeHtml(invoice.pdf_url) + '" aria-label="Download PDF" title="Download PDF"><i class="far fa-file-pdf"></i></a>',
            '<a class="invoice-mobile-action invoice-mobile-action-print icon-btn icon-btn-primary" href="' + escapeHtml(invoice.print_url) + '" aria-label="Print invoice" title="Print invoice"><i class="fas fa-print"></i></a>',
            '<a class="invoice-mobile-action invoice-mobile-action-edit icon-btn icon-btn-warning" href="' + escapeHtml(invoice.edit_url) + '" aria-label="Edit invoice" title="Edit invoice"><i class="fas fa-pen"></i></a>',
            '<button class="invoice-mobile-action invoice-mobile-action-share icon-btn icon-btn-success invoice-native-share" type="button" data-invoice-number="' + escapeHtml(invoice.invoice_number) + '" data-customer-name="' + escapeHtml(invoice.customer_name) + '" data-net-payable="' + escapeHtml(invoice.net_payable_amount) + '" data-pdf-url="' + escapeHtml(invoice.pdf_url) + '" aria-label="Share invoice" title="Share invoice"><i class="fas fa-share-alt"></i></button>',
            '<form method="POST" action="' + escapeHtml(invoice.delete_url) + '" data-confirm="Are you sure you want to delete this invoice?">',
            '<input type="hidden" name="_token" value="' + escapeHtml(csrfToken) + '">',
            '<input type="hidden" name="_method" value="DELETE">',
            '<button class="invoice-mobile-action invoice-mobile-action-delete icon-btn icon-btn-danger" type="submit" aria-label="Delete invoice" title="Delete invoice"><i class="fas fa-trash"></i></button>',
            '</form>',
            '</div>',
        ].join('');
    }

    function renderMobileCards(invoices) {
        if (!mobileCards) {
            return;
        }

        mobileCards.innerHTML = invoices.map(function (invoice) {
            const status = invoice.payment_status || 'unpaid';

            return [
                '<article class="invoice-mobile-card">',
                '<header class="invoice-mobile-card-header">',
                '<h2>' + escapeHtml(invoice.invoice_number) + '</h2>',
                '<span class="status-badge status-' + escapeHtml(status) + '">' + escapeHtml(status.charAt(0).toUpperCase() + status.slice(1)) + '</span>',
                '</header>',
                '<div class="invoice-mobile-info">',
                '<div class="invoice-mobile-row invoice-mobile-row-customer"><span class="invoice-mobile-icon invoice-mobile-icon-customer"><i class="far fa-user"></i></span><strong>' + escapeHtml(invoice.customer_name) + '</strong></div>',
                '<div class="invoice-mobile-row invoice-mobile-row-gst"><span class="invoice-mobile-icon invoice-mobile-icon-gst"><i class="fas fa-receipt"></i></span><span>GST</span><span class="gst-badge">' + escapeHtml(invoice.customer_gst) + '</span></div>',
                '<div class="invoice-mobile-row invoice-mobile-row-date"><span class="invoice-mobile-icon invoice-mobile-icon-date"><i class="far fa-calendar-alt"></i></span><span>Date</span><strong>' + escapeHtml(invoice.invoice_date) + '</strong></div>',
                '<div class="invoice-mobile-row invoice-mobile-row-payable"><span class="invoice-mobile-icon invoice-mobile-icon-payable"><i class="fas fa-rupee-sign"></i></span><span>Net Payable</span><strong>Rs. ' + escapeHtml(invoice.net_payable_amount) + '</strong></div>',
                '</div>',
                renderMobileActions(invoice),
                '</article>',
            ].join('');
        }).join('');
    }

    function renderInvoices(invoices) {
        tableBody.innerHTML = invoices.map(function (invoice) {
            const status = invoice.payment_status || 'unpaid';

            return [
                '<tr>',
                '<td data-label="Invoice No.">' + escapeHtml(invoice.invoice_number) + '</td>',
                '<td data-label="Date">' + escapeHtml(invoice.invoice_date) + '</td>',
                '<td data-label="Customer">' + escapeHtml(invoice.customer_name) + '</td>',
                '<td data-label="GST"><span class="gst-badge">' + escapeHtml(invoice.customer_gst) + '</span></td>',
                '<td data-label="Net Payable">Rs. ' + escapeHtml(invoice.net_payable_amount) + '</td>',
                '<td data-label="Status"><span class="status-badge status-' + escapeHtml(status) + '">' + escapeHtml(status.charAt(0).toUpperCase() + status.slice(1)) + '</span></td>',
                '<td data-label="Actions">' + renderActions(invoice) + '</td>',
                '</tr>',
            ].join('');
        }).join('');

        renderMobileCards(invoices);
    }

    function fetchInvoices(search) {
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

    perPageSelect?.addEventListener('change', function () {
        perPageSelect.form?.submit();
    });
})();
