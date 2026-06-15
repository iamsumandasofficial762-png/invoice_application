(function () {
    function safeFileName(invoiceNumber) {
        return 'invoice-' + invoiceNumber.replace(/[\/\\:*?"<>|]/g, '-') + '.pdf';
    }

    function buildShareData(button) {
        const invoiceNumber = button.dataset.invoiceNumber || '';
        const customerName = button.dataset.customerName || '';
        const netPayable = button.dataset.netPayable || '';
        const pdfUrl = button.dataset.pdfUrl || window.location.href;
        const shareText = [
            'Invoice No: ' + invoiceNumber,
            'Customer: ' + customerName,
            'Net Payable: Rs. ' + netPayable,
        ].join('\n');

        return {
            invoiceNumber: invoiceNumber,
            customerName: customerName,
            netPayable: netPayable,
            pdfUrl: pdfUrl,
            shareText: shareText,
            fileName: safeFileName(invoiceNumber),
        };
    }

    function setLoading(button, isLoading) {
        button.disabled = isLoading;
        button.classList.toggle('is-loading', isLoading);
        button.setAttribute('aria-busy', isLoading ? 'true' : 'false');
    }

    function copyToClipboard(text) {
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text);
        }

        const input = document.createElement('textarea');
        input.value = text;
        input.setAttribute('readonly', '');
        input.style.position = 'fixed';
        input.style.opacity = '0';
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);

        return Promise.resolve();
    }

    function showTemporaryStatus(button, message) {
        const previousTitle = button.getAttribute('title') || '';

        button.setAttribute('title', message);
        button.dataset.shareStatus = message;

        window.setTimeout(function () {
            button.setAttribute('title', previousTitle);
            delete button.dataset.shareStatus;
        }, 2500);
    }

    async function copyPdfLinkOrOpen(shareData) {
        try {
            await copyToClipboard(shareData.pdfUrl);
        } catch (error) {
            window.location.href = shareData.pdfUrl;
        }
    }

    function isPdfResponse(response, blob) {
        const contentType = response.headers.get('content-type') || blob.type || '';

        return contentType.toLowerCase().includes('application/pdf');
    }

    async function fetchPdfFile(shareData) {
        const response = await fetch(shareData.pdfUrl, {
            cache: 'no-store',
            credentials: 'same-origin',
            headers: {
                Accept: 'application/pdf',
            },
        });

        if (!response.ok) {
            throw new Error('Unable to fetch PDF');
        }

        const blob = await response.blob();

        if (!isPdfResponse(response, blob)) {
            throw new Error('PDF route did not return a PDF file');
        }

        return new File([blob], shareData.fileName, {
            type: 'application/pdf',
        });
    }

    async function sharePdf(button) {
        const shareData = buildShareData(button);

        setLoading(button, true);

        try {
            const file = await fetchPdfFile(shareData);

            if (navigator.share && navigator.canShare && navigator.canShare({ files: [file] })) {
                await navigator.share({
                    title: 'Invoice ' + shareData.invoiceNumber,
                    text: shareData.shareText,
                    files: [file],
                });
                return;
            }

            if (navigator.share) {
                try {
                    await navigator.share({
                        title: 'Invoice ' + shareData.invoiceNumber,
                        text: shareData.shareText,
                        url: shareData.pdfUrl,
                    });
                } catch (error) {
                    if (error && (error.name === 'AbortError' || error.name === 'NotAllowedError')) {
                        return;
                    }

                    throw error;
                }
                return;
            }

            await copyPdfLinkOrOpen(shareData);
            showTemporaryStatus(button, 'PDF link copied.');
        } catch (error) {
            if (error && (error.name === 'AbortError' || error.name === 'NotAllowedError')) {
                return;
            }

            await copyPdfLinkOrOpen(shareData);
            showTemporaryStatus(button, 'PDF link copied.');
        } finally {
            setLoading(button, false);
        }
    }

    document.addEventListener('click', function (event) {
        const button = event.target.closest('.invoice-native-share');

        if (!button) {
            return;
        }

        event.preventDefault();
        sharePdf(button);
    });
})();
