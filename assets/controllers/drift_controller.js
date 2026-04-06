import { Controller } from '@hotwired/stimulus';
import Drift from 'drift-zoom';

export default class extends Controller {
    static targets = ['mainImage', 'thumbnail'];

    connect() {
        this.driftInstance = null;
        this.initDrift();
    }

    disconnect() {
        this.destroyDrift();
    }

    initDrift() {
        if (!this.hasMainImageTarget) {
            return;
        }

        this.destroyDrift();

        this.driftInstance = new Drift(this.mainImageTarget, {
            paneContainer: this.element.querySelector('.image-zoom-container'),
            inlinePane: false,
            hoverBoundingBox: true,
            containInline: true,
        });
    }

    destroyDrift() {
        if (this.driftInstance && typeof this.driftInstance.destroy === 'function') {
            this.driftInstance.destroy();
            this.driftInstance = null;
        }
    }

    changeImage(event) {
        const button = event.currentTarget;
        const image = button.dataset.image;
        const zoom = button.dataset.zoom;

        if (!image || !this.hasMainImageTarget) {
            return;
        }

        this.mainImageTarget.src = image;
        this.mainImageTarget.setAttribute('data-zoom', zoom || image);

        this.thumbnailTargets.forEach((thumbnail) => {
            thumbnail.classList.remove('active');
        });

        button.classList.add('active');

        this.initDrift();
    }
}
