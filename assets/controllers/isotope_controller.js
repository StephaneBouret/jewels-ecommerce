import { Controller } from '@hotwired/stimulus';
import Isotope from 'isotope-layout';
import imagesLoaded from 'imagesloaded';

export default class extends Controller {
    static targets = ['container', 'filter'];

    connect() {
        if (!this.hasContainerTarget) {
            return;
        }

        const layoutMode = this.element.dataset.isotopeLayout ?? 'fitRows';
        const defaultFilter = this.element.dataset.isotopeFilter ?? '*';

        this.iso = new Isotope(this.containerTarget, {
            itemSelector: '.isotope-item',
            layoutMode: layoutMode,
            filter: defaultFilter,
        });

        imagesLoaded(this.containerTarget, () => {
            this.iso.layout();
        });
    }

    filter(event) {
        const button = event.currentTarget;
        const filterValue = button.dataset.filter ?? '*';

        if (!this.iso) {
            return;
        }

        this.iso.arrange({
            filter: filterValue,
        });

        this.filterTargets.forEach((target) => {
            target.classList.remove('filter-active');
        });

        button.classList.add('filter-active');
    }
}
