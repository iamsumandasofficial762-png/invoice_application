(function () {
    const pdfFileCache = new Map();
    const readyPdfFiles = new Map();

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

    function setPreparing(button, isPreparing) {
        button.classList.toggle('is-loading', isPreparing);
        button.setAttribute('aria-busy', isPreparing ? 'true' : 'false');
        button.dataset.shareReady = isPreparing ? 'false' : 'true';
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

    async function copyPdfLinkOrOpen(shareData, message) {
        try {
            await copyToClipboard(shareData.pdfUrl);
            alert(message);
        } catch (error) {
            window.location.href = shareData.pdfUrl;
        }
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

        await copyPdfLinkOrOpen(shareData, 'PDF link copied. Native PDF file sharing is not supported on this browser.');
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
            pdfFileCache.set(shareData.pdfUrl, fetchPdfFile(shareData)
                .then(function (file) {
                    readyPdfFiles.set(shareData.pdfUrl, file);
                    return file;
                })
                .catch(function (error) {
                    pdfFileCache.delete(shareData.pdfUrl);
                    readyPdfFiles.delete(shareData.pdfUrl);
                    throw error;
                }));
        }

        return pdfFileCache.get(shareData.pdfUrl);
    }

    async function openNativeShare(shareData) {
        const readyFile = readyPdfFiles.get(shareData.pdfUrl);

        if (readyFile && navigator.canShare && navigator.canShare({ files: [readyFile] })) {
            await navigator.share({
                title: 'Invoice ' + shareData.invoiceNumber,
                text: shareData.shareText,
                files: [readyFile],
            });
            return;
        }

        throw new Error('Native PDF file sharing is not ready or not supported.');
    }

    function prepareShareButton(button) {
        if (!navigator.share || button.dataset.sharePreparing === 'true' || button.dataset.shareReady === 'true') {
            return;
        }

        button.dataset.sharePreparing = 'true';
        setPreparing(button, true);

        preparePdfFile(buildShareData(button))
            .then(function () {
                setPreparing(button, false);
            })
            .catch(function () {
                setPreparing(button, false);
                button.dataset.shareReady = 'false';
            })
            .finally(function () {
                button.dataset.sharePreparing = 'false';
            });
    }

    async function sharePdf(button) {
        const shareData = buildShareData(button);

        setLoading(button, true);

        try {
            if (!navigator.share) {
                await copyPdfLinkOrOpen(shareData, 'PDF link copied. Native sharing is not supported on this browser.');
                return;
            }

            if (!readyPdfFiles.has(shareData.pdfUrl)) {
                prepareShareButton(button);
                alert('PDF is preparing for native sharing. Please click Share again in a moment.');
                return;
            }

            await openNativeShare(shareData);
        } catch (error) {
            if (error && error.name === 'AbortError') {
                return;
            }

            alert('Native PDF file sharing is not supported or not ready in this browser.');
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

        if (button) {
            prepareShareButton(button);
        }
    }, true);

    document.addEventListener('focusin', function (event) {
        const button = event.target.closest('.invoice-native-share');

        if (button) {
            prepareShareButton(button);
        }
    });

    document.addEventListener('pointerdown', function (event) {
        const button = event.target.closest('.invoice-native-share');

        if (button) {
            prepareShareButton(button);
        }
    });

    function prepareExistingShareButtons() {
        document.querySelectorAll('.invoice-native-share').forEach(prepareShareButton);
    }

    prepareExistingShareButtons();
    document.addEventListener('DOMContentLoaded', prepareExistingShareButtons);

    const observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            mutation.addedNodes.forEach(function (node) {
                if (!(node instanceof Element)) {
                    return;
                }

                if (node.matches('.invoice-native-share')) {
                    prepareShareButton(node);
                }

                node.querySelectorAll?.('.invoice-native-share').forEach(prepareShareButton);
            });
        });
    });

    observer.observe(document.documentElement, {
        childList: true,
        subtree: true,
    });
})();
