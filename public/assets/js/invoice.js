(function () {
    const itemsBody = document.querySelector('[data-items-body]');
    const rowTemplate = document.getElementById('item-row-template');
    const addItemButton = document.querySelector('[data-add-item]');
    const ajaxCustomerForm = document.getElementById('ajax-customer-form');
    const signatureSelect = document.getElementById('signature_image');
    const signaturePreview = document.getElementById('signaturePreview');
    const signaturePlaceholder = document.getElementById('signaturePlaceholder');
    const amountWordsPreview = document.querySelector('[data-amount-words-preview]');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function money(value) {
        return Number(value || 0).toFixed(2);
    }

    function wordsUnderHundred(number) {
        const ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        if (number < 20) {
            return ones[number];
        }

        return (tens[Math.floor(number / 10)] + ' ' + ones[number % 10]).trim();
    }

    function numberToIndianWords(number) {
        number = Math.round(Number(number || 0));

        if (number === 0) {
            return 'Rupees Zero Only';
        }

        const parts = [];
        const groups = [
            [10000000, 'Crore'],
            [100000, 'Lakh'],
            [1000, 'Thousand'],
            [100, 'Hundred'],
        ];

        groups.forEach(function (group) {
            const value = group[0];
            const label = group[1];

            if (number >= value) {
                parts.push(numberToIndianWords(Math.floor(number / value)).replace('Rupees ', '').replace(' Only', '') + ' ' + label);
                number = number % value;
            }
        });

        if (number > 0) {
            parts.push(wordsUnderHundred(number));
        }

        return 'Rupees ' + parts.join(' ').trim() + ' Only';
    }

    function refreshSignaturePreview() {
        if (!signatureSelect || !signaturePreview) {
            return;
        }

        const selected = signatureSelect.options[signatureSelect.selectedIndex];
        const frame = signaturePreview.closest('.signature-image-frame');
        const src = selected?.dataset.signatureSrc || '';

        if (!src) {
            signaturePreview.removeAttribute('src');
            frame?.classList.remove('has-signature');
            if (signaturePlaceholder) {
                signaturePlaceholder.textContent = 'Select signature';
            }
            return;
        }

        signaturePreview.src = src;
        frame?.classList.add('has-signature');
    }

    function isMobileItemsView() {
        return window.matchMedia('(max-width: 767px)').matches;
    }

    function refreshRows() {
        if (!itemsBody) {
            return;
        }

        const shouldRenumberVisibleRows = !isMobileItemsView();
        const rows = itemsBody.querySelectorAll('[data-item-row]');

        rows.forEach(function (row, index) {
            if (shouldRenumberVisibleRows) {
                row.querySelector('.sr-no').textContent = index + 1;
            }

            row.querySelectorAll('input, textarea').forEach(function (field) {
                field.name = field.name.replace(/items\[[^\]]+\]/, 'items[' + index + ']');
            });

            const removeButton = row.querySelector('[data-remove-item]');
            removeButton.disabled = rows.length === 1;
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

        if (amountWordsPreview) {
            amountWordsPreview.textContent = numberToIndianWords(gross);
        }
    }

    function addItemRow() {
        const rows = itemsBody.querySelectorAll('[data-item-row]');
        const index = rows.length;
        const itemNumber = index + 1;
        const html = rowTemplate.innerHTML.replaceAll('__INDEX__', index);
        const isMobile = isMobileItemsView();
        const position = isMobile ? 'afterbegin' : 'beforeend';

        itemsBody.insertAdjacentHTML(position, html);
        const newRow = isMobile
            ? itemsBody.querySelector('[data-item-row]')
            : itemsBody.querySelector('[data-item-row]:last-child');

        if (newRow) {
            newRow.querySelector('.sr-no').textContent = itemNumber;
        }

        refreshRows();
        recalculate();
    }

    function openModal(id) {
        document.getElementById(id)?.classList.add('is-open');
    }

    function closeModal(id) {
        document.getElementById(id)?.classList.remove('is-open');
    }

    signatureSelect?.addEventListener('change', refreshSignaturePreview);
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
                if (window.selectInvoiceCustomer) {
                    window.selectInvoiceCustomer(data.customer);
                }
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

    refreshSignaturePreview();
    refreshRows();
    recalculate();
})();
