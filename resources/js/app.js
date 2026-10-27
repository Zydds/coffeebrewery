import './bootstrap';

import Alpine from 'alpinejs';

import { animate, stagger } from 'animejs';

// ── Animation helpers ─────────────────────────────────────
// Single reduced-motion choke point: every animation in this file
// funnels through play(), so users who asked for reduced motion get
// instant state changes instead of tweens (CSS can't stop WAAPI
// animations — the guard has to live in JS).
const prefersReduced = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const play = (targets, params) => {
    if (!targets || prefersReduced()) {
        return null;
    }

    const list = targets instanceof Element ? [targets]
        : Array.isArray(targets) ? targets
        : Array.from(targets);

    if (!list.length) {
        return null;
    }

    return animate(list, params); // thenable: `await anim` resolves on completion
};

// After a scroll reveal settles, release our styling so the CSS
// fallback state (.in-view) and hover utilities like hover:-translate-y-1
// own the element again — otherwise an inline transform would kill
// the card lift on hover.
const releaseMotion = (targets, anim) => {
    anim?.cancel?.();

    (targets instanceof Element ? [targets] : Array.isArray(targets) ? targets : Array.from(targets))
        .forEach((el) => {
            el.style.removeProperty('opacity');
            el.style.removeProperty('transform');
        });
};

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
        if (this.loading) {
            return;
        }

        const card = this.$refs.card;

        // Card swap: slide the current recipe out first so the new one
        // arrives with a fade instead of the content popping in place.
        if (card && this.recipe) {
            await play(card, { opacity: 0, translateY: -10, duration: 150, ease: 'inQuad' });
        }

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

        // Alpine swaps x-show on the next tick — animate once the new
        // content is actually on screen. Unconditional (no display gate):
        // error/empty states keep the card hidden and a hidden element
        // animating is harmless, while a gate that misreads Alpine's timing
        // could strand the card at opacity 0.
        if (card) {
            await this.$nextTick();

            play(card, { opacity: [0, 1], translateY: [12, 0], duration: 320, ease: 'outCubic' });
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
    animating: false,

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
        this.go(1);
    },

    next() {
        this.go(1);
    },

    back() {
        this.go(-1);
    },

    // The currently rendered question card (each step div is targeted
    // by its x-show expression, which Alpine keeps in the DOM).
    stepEl() {
        return this.$refs.questions?.querySelector(`[x-show="current === '${this.current}'"]`) ?? null;
    },

    // Step transition: current question slides out (direction of travel),
    // state advances, next question slides in — replaces the old hardcoded
    // setTimeout(220) guess. Under reduced motion play() returns null and
    // each await resolves instantly, so the wizard advances without tweens.
    async go(dir) {
        if (this.animating) {
            return;
        }

        if (dir > 0 && !this.answers[this.current]) {
            return;
        }

        if (dir < 0 && this.step <= 0) {
            return;
        }

        this.animating = true;

        try {
            const fromEl = this.stepEl();

            await play(fromEl, {
                opacity: 0,
                translateX: dir > 0 ? -18 : 18,
                duration: 150,
                ease: 'inQuad',
            });

            if (dir > 0) {
                if (this.step + 1 < this.total) {
                    this.step++;
                } else {
                    await this.submit(); // results animation runs inside submit()
                }
            } else {
                this.step--;
            }

            await this.$nextTick();

            if (this.finished) {
                return;
            }

            const toEl = this.stepEl();

            if (toEl) {
                await play(toEl, {
                    opacity: [0, 1],
                    translateX: toEl === fromEl ? [0, 0] : [dir > 0 ? 18 : -18, 0],
                    duration: toEl === fromEl ? 200 : 260,
                    ease: 'outCubic',
                });
            }
        } finally {
            this.animating = false;
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

        if (this.finished) {
            await this.$nextTick();
            play(this.$refs.results, { opacity: [0, 1], translateY: [14, 0], duration: 320, ease: 'outCubic' });
        }
    },

    restart() {
        this.step = 0;
        this.answers = { ingredients: null, time: null, gear: null, taste: null, drink: null };
        this.matches = [];
        this.matchIndex = 0;
        this.finished = false;
        this.error = '';

        // Steps ended the flow at opacity 0 (their exit animations committed
        // inline styles). Strip them synchronously — the step divs stay in
        // the DOM under x-show, so there's nothing to wait for. (A $nextTick
        // here proved unreliable when restart() runs from its @click handler:
        // the callback never fired, stranding question 1 at opacity 0.)
        this.$refs.questions?.querySelectorAll('div[x-show]').forEach((el) => {
            el.style.removeProperty('opacity');
            el.style.removeProperty('transform');
        });
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
    // [data-reveal]        → the element itself tweens
    // [data-reveal-stagger] → its children cascade (stagger 60ms)
    // The .in-view class remains the CSS fallback (reduced motion,
    // no IntersectionObserver) — JS owns the tween itself.
    const revealEls = document.querySelectorAll('[data-reveal], [data-reveal-stagger]');

    if ('IntersectionObserver' in window && revealEls.length) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                const el = entry.target;
                const staggered = el.hasAttribute('data-reveal-stagger');
                const targets = staggered ? Array.from(el.children) : [el];
                const entering = entry.isIntersecting;

                el.classList.toggle('in-view', entering);

                const anim = play(targets, entering
                    ? {
                        opacity: [0, 1],
                        translateY: [24, 0],
                        duration: 700,
                        delay: staggered ? stagger(60) : 0,
                        ease: 'outCubic',
                    }
                    : {
                        // Explicit from-values: the .in-view class flip above
                        // already set CSS to the hidden state, so a to-only
                        // value would animate 0 → 0 (invisible).
                        opacity: [1, 0],
                        translateY: [0, 24],
                        duration: 450,
                        delay: staggered ? stagger(30) : 0,
                        ease: 'inQuad',
                    });

                if (anim) {
                    anim.then(() => releaseMotion(targets, anim));
                }
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
