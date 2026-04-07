import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["results", "search", "sort"];
    static values = {
        url: String,
    };

    connect() {
        this.category = "all";
        this.searchTimeout = null;
    }

    changeCategory(event) {
        const button = event.currentTarget;
        this.category = button.dataset.category ?? "all";

        this.element.querySelectorAll(".category-link").forEach((link) => {
            link.classList.remove("active");
        });

        button.classList.add("active");

        this.loadProducts();
    }

    sort() {
        this.loadProducts();
    }

    search() {
        clearTimeout(this.searchTimeout);

        this.searchTimeout = setTimeout(() => {
            this.loadProducts();
        }, 300);
    }

    async loadProducts() {
        const params = new URLSearchParams({
            category: this.category ?? "all",
            sort: this.hasSortTarget ? this.sortTarget.value : "latest",
            search: this.hasSearchTarget ? this.searchTarget.value : "",
        });

        const response = await fetch(`${this.urlValue}?${params.toString()}`, {
            headers: {
                "X-Requested-With": "XMLHttpRequest",
            },
        });

        if (!response.ok) {
            return;
        }

        const html = await response.text();
        this.resultsTarget.innerHTML = html;
    }
}
