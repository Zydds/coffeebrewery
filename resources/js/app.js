import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('surpriseModal', () => ({
    open: false,
    loading: false,
    error: '',
    empty: false,
    recipe: null,

    show() {
        this.open = true;

        if (!this.recipe && !this.loading) {
            this.roll();
        }
    },

    async roll(exclude = null) {
        this.loading = true;
        this.error = '';
        this.empty = false;

        try {
            let url = '/api/recipes/random';

            if (exclude) {
                url += '?exclude=' + encodeURIComponent(exclude);
            }

            const response = await fetch(url, {
                headers: { 'Accept': 'application/json' },
            });

            if (response.status === 404) {
                this.empty = true;
                this.recipe = null;
                return;
            }

            if (!response.ok) {
                throw new Error('Request failed with status ' + response.status);
            }

            const body = await response.json();
            this.recipe = body.data;
        } catch (e) {
            this.error = 'Could not load a recipe. Try again.';
        } finally {
            this.loading = false;
        }
    },
}));

Alpine.start();
