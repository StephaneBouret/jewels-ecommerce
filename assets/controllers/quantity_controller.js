import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input'];

    connect() {
        this.sanitize();
    }

    increase() {
        if (!this.hasInputTarget) {
            return;
        }

        const input = this.inputTarget;
        const currentValue = this.getSafeValue();
        const max = this.getMax();

        if (currentValue < max) {
            input.value = currentValue + 1;
        }

        this.sanitize();
    }

    decrease() {
        if (!this.hasInputTarget) {
            return;
        }

        const input = this.inputTarget;
        const currentValue = this.getSafeValue();
        const min = this.getMin();

        if (currentValue > min) {
            input.value = currentValue - 1;
        }

        this.sanitize();
    }

    sanitize() {
        if (!this.hasInputTarget) {
            return;
        }

        const input = this.inputTarget;
        const min = this.getMin();
        const max = this.getMax();

        let value = parseInt(input.value, 10);

        if (Number.isNaN(value)) {
            value = min;
        }

        if (value < min) {
            value = min;
        }

        if (value > max) {
            value = max;
        }

        input.value = value;
    }

    getSafeValue() {
        const value = parseInt(this.inputTarget.value, 10);
        return Number.isNaN(value) ? this.getMin() : value;
    }

    getMin() {
        const min = parseInt(this.inputTarget.min, 10);
        return Number.isNaN(min) ? 1 : min;
    }

    getMax() {
        const max = parseInt(this.inputTarget.max, 10);
        return Number.isNaN(max) ? Number.MAX_SAFE_INTEGER : max;
    }
}
