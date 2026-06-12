(function () {
    const pdfFileCache = new Map();

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

    async function shareUrlOrCopy(shareData) {
        if (navigator.share) {
            try {
                await navigator.share({
                    title: 'Invoice ' + shareData.invoiceNumber,
                    text: shareData.shareText,
                    url: shareData.pdfUrl,
                });
                return;
            } catch (error) {
                if (error && error.name === 'AbortError') {
                    return;
                }
            }
        }

        await copyToClipboard(shareData.pdfUrl);
        alert('PDF link copied. Native PDF file sharing is not supported on this browser.');
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

    function preparePdfFile(shareData) {
        if (!pdfFileCache.has(shareData.pdfUrl)) {
            pdfFileCache.set(shareData.pdfUrl, fetchPdfFile(shareData).catch(function (error) {
                pdfFileCache.delete(shareData.pdfUrl);
                throw error;
            }));
        }

        return pdfFileCache.get(shareData.pdfUrl);
    }

    async function sharePdf(button) {
        const shareData = buildShareData(button);

        setLoading(button, true);

        try {
            if (!navigator.share) {
                await copyToClipboard(shareData.pdfUrl);
                alert('PDF link copied. Native sharing is not supported on this browser.');
                return;
            }

            try {
                var file = await preparePdfFile(shareData);
            } catch (error) {
                await shareUrlOrCopy(shareData);
                return;
            }

            if (file && navigator.canShare && navigator.canShare({ files: [file] })) {
                try {
                    await navigator.share({
                        title: 'Invoice ' + shareData.invoiceNumber,
                        text: shareData.shareText,
                        files: [file],
                    });
                    return;
                } catch (error) {
                    if (error && error.name === 'AbortError') {
                        return;
                    }
                }
            }

            await shareUrlOrCopy(shareData);
        } catch (error) {
            if (error && error.name === 'AbortError') {
                return;
            }

            alert('Unable to prepare invoice PDF for sharing.');
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

    document.addEventListener('pointerenter', function (event) {
        const button = event.target.closest('.invoice-native-share');

        if (button && navigator.share) {
            preparePdfFile(buildShareData(button)).catch(function () {});
        }
    }, true);

    document.addEventListener('focusin', function (event) {
        const button = event.target.closest('.invoice-native-share');

        if (button && navigator.share) {
            preparePdfFile(buildShareData(button)).catch(function () {});
        }
    });

    document.addEventListener('pointerdown', function (event) {
        const button = event.target.closest('.invoice-native-share');

        if (button && navigator.share) {
            preparePdfFile(buildShareData(button)).catch(function () {});
        }
    });
})();
