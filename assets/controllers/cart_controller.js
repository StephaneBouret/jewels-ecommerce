import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['content'];
    static values = {
        addUrl: String,
        updateUrl: String,
        removeUrl: String,
        clearUrl: String,
    };

    async add(event) {
        event.preventDefault();

        const button = event.currentTarget;
        const id = button.dataset.id;
        const qty = this.resolveQuantity(button);

        await this.postAndRefresh(this.buildUrl(this.addUrlValue, id), { qty });
    }

    async increase(event) {
        const id = event.currentTarget.dataset.id;
        const input = this.findQtyInput(id);
        if (!input) return;

        const max = parseInt(input.max || '999999', 10);
        const next = Math.min(max, (parseInt(input.value || '1', 10) || 1) + 1);

        await this.postAndRefresh(this.buildUrl(this.updateUrlValue, id), { qty: next });
    }

    async decrease(event) {
        const id = event.currentTarget.dataset.id;
        const input = this.findQtyInput(id);
        if (!input) return;

        const current = parseInt(input.value || '1', 10) || 1;
        const next = current - 1;

        if (next <= 0) {
            await this.postAndRefresh(this.buildUrl(this.removeUrlValue, id));
            return;
        }

        await this.postAndRefresh(this.buildUrl(this.updateUrlValue, id), { qty: next });
    }

    async changeQty(event) {
        const input = event.currentTarget;
        const id = input.dataset.id;
        const min = parseInt(input.min || '1', 10);
        const max = parseInt(input.max || '999999', 10);

        let qty = parseInt(input.value || String(min), 10);
        if (Number.isNaN(qty)) qty = min;
        qty = Math.max(min, Math.min(max, qty));

        await this.postAndRefresh(this.buildUrl(this.updateUrlValue, id), { qty });
    }

    async remove(event) {
        const id = event.currentTarget.dataset.id;
        await this.postAndRefresh(this.buildUrl(this.removeUrlValue, id));
    }

    async clear(event) {
        if (event) {
            event.preventDefault();
        }

        await this.postAndRefresh(this.clearUrlValue);
    }

    async postAndRefresh(url, data = {}) {
        const formData = new FormData();
        Object.entries(data).forEach(([key, value]) => formData.append(key, value));

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData,
        });

        if (!response.ok) {
            return;
        }

        const payload = await response.json();

        if (this.hasContentTarget && payload.html) {
            this.contentTarget.innerHTML = payload.html;
        }

        this.updateCartCount(payload.count);
    }

    buildUrl(template, id = null) {
        if (id === null) {
            return template;
        }

        return template.replace(/0$/, String(id));
    }

    resolveQuantity(button) {
        const selector = button.dataset.qtySelector;
        if (!selector) {
            return 1;
        }

        const input = document.querySelector(selector);
        if (!input) {
            return 1;
        }

        const qty = parseInt(input.value || '1', 10);
        return Number.isNaN(qty) ? 1 : Math.max(1, qty);
    }

    findQtyInput(id) {
        if (!this.hasContentTarget) {
            return null;
        }

        return this.contentTarget.querySelector(`input.quantity-input[data-id="${id}"]`);
    }

    updateCartCount(count) {
        document.querySelectorAll('[data-cart-count]').forEach((element) => {
            element.textContent = count;
        });
    }
}
