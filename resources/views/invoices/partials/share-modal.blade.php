<div class="invoice-share-modal" data-share-modal aria-hidden="true">
    <div class="invoice-share-card" role="dialog" aria-modal="true" aria-labelledby="invoiceShareTitle">
        <div class="invoice-share-header">
            <div>
                <h2 id="invoiceShareTitle">Share Invoice</h2>
                <p>Share this invoice via</p>
            </div>
            <button class="invoice-share-close" type="button" data-share-close aria-label="Close share options">X</button>
        </div>

        <div class="invoice-share-options" aria-label="Share options">
            <button class="invoice-share-option invoice-share-whatsapp" type="button" data-share-whatsapp>
                <span>W</span>
                <strong>WhatsApp</strong>
            </button>
            <button class="invoice-share-option invoice-share-gmail" type="button" data-share-gmail>
                <span>G</span>
                <strong>Email</strong>
            </button>
            <button class="invoice-share-option invoice-share-telegram" type="button" data-share-telegram>
                <span>T</span>
                <strong>Telegram</strong>
            </button>
            <button class="invoice-share-option invoice-share-facebook" type="button" data-share-facebook>
                <span>f</span>
                <strong>Facebook</strong>
            </button>
            <button class="invoice-share-option invoice-share-other" type="button" data-share-other>
                <span>...</span>
                <strong>Other</strong>
            </button>
        </div>

        <div class="invoice-share-copy-panel">
            <label for="invoiceShareLink">Copy invoice link</label>
            <div class="invoice-share-copy-row">
                <input id="invoiceShareLink" type="text" value="" readonly data-share-link>
                <button class="invoice-share-copy-button" type="button" data-share-copy aria-label="Copy invoice link">Copy</button>
            </div>
        </div>

        <p class="invoice-share-toast" data-share-toast role="status" aria-live="polite"></p>
    </div>
</div>
