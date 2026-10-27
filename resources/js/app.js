import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('surpriseSection', (options = {}) => ({
    cheap: options.cheap ?? false,
    quick: options.quick ?? false,
    loading: false,
    error: '',
    empty: false,
    recipe: null,
    rolled: false,

    init() {
        this.roll();
    },

    async roll(exclude = null) {
        this.loading = true;
        this.error = '';
        this.empty = false;

        try {
            const params = new URLSearchParams();

            if (exclude) {
                params.set('exclude', exclude);
            }

            if (this.cheap) {
                params.set('cheap', '1');
            }

            if (this.quick) {
                params.set('quick', '1');
            }

            const query = params.toString();
            const url = '/api/recipes/random' + (query ? '?' + query : '');

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
            this.error = window.APP_I18N?.recipeLoadError ?? 'Could not load a recipe. Try again.';
        } finally {
            this.loading = false;
        }
    },

    rollNext() {
        this.rolled = true;
        this.roll(this.recipe?.id ?? null);
    },
}));

Alpine.data('servings', (coffee, water) => ({
    n: 1,
    coffee,
    water,
}));

Alpine.data('brewFinder', () => ({
    step: 0,
    answers: { ingredients: null, time: null, gear: null, taste: null, drink: null },
    loading: false,
    error: '',
    finished: false,
    matches: [],
    matchIndex: 0,

    get steps() {
        const steps = ['ingredients', 'time'];

        if (this.answers.ingredients && this.answers.ingredients !== 'sachet') {
            steps.push('gear');
        }

        return [...steps, 'taste', 'drink'];
    },

    get total() {
        return this.steps.length;
    },

    get current() {
        return this.steps[this.step];
    },

    get match() {
        return this.matches[this.matchIndex] ?? null;
    },

    pick(key, value) {
        this.answers[key] = value;
        window.setTimeout(() => this.next(), 220);
    },

    next() {
        if (!this.answers[this.current]) {
            return;
        }

        if (this.step + 1 < this.total) {
            this.step++;
        } else {
            this.submit();
        }
    },

    back() {
        if (this.step > 0) {
            this.step--;
        }
    },

    async submit() {
        this.loading = true;
        this.error = '';
        this.finished = false;

        try {
            const params = new URLSearchParams();

            Object.entries(this.answers).forEach(([key, value]) => {
                if (!value) {
                    return;
                }

                if (key === 'gear' && this.answers.ingredients === 'sachet') {
                    return;
                }

                params.set(key, value);
            });

            const response = await fetch('/api/recipes/suggest?' + params.toString(), {
                headers: { 'Accept': 'application/json' },
            });

            if (!response.ok) {
                throw new Error('Request failed with status ' + response.status);
            }

            const body = await response.json();
            this.matches = body.data ?? [];
            this.matchIndex = 0;
            this.finished = true;
        } catch (e) {
            this.error = window.APP_I18N?.matchError ?? 'Could not find a match. Try again.';
        } finally {
            this.loading = false;
        }
    },

    restart() {
        this.step = 0;
        this.answers = { ingredients: null, time: null, gear: null, taste: null, drink: null };
        this.matches = [];
        this.matchIndex = 0;
        this.finished = false;
        this.error = '';
    },
}));

Alpine.start();

// Scroll to the catalog section that a search/filter belongs to.
document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);

    if (params.has('q') || params.has('category')) {
        document.getElementById('bean-catalog')?.scrollIntoView();
    }

    if (params.has('method_q')) {
        document.getElementById('brew-methods')?.scrollIntoView();
    }

    // Fade sections in as they enter the viewport.
    const revealEls = document.querySelectorAll('[data-reveal]');

    if ('IntersectionObserver' in window && revealEls.length) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                entry.target.classList.toggle('in-view', entry.isIntersecting);
            });
        }, {
            threshold: 0,
            rootMargin: '0px 0px -60px 0px',
        });

        revealEls.forEach((el) => observer.observe(el));
    } else {
        revealEls.forEach((el) => el.classList.add('in-view'));
    }
});
