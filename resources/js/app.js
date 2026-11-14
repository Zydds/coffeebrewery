import './bootstrap';

import Alpine from 'alpinejs';

import { animate, stagger, cubicBezier } from 'animejs';
import { easeOutShared } from './easing';

// The one shared curve of the redesign, mirrored from --ease-out in
// app.css. Passed to every JS tween so CSS and JS can never drift.
const easeOut = cubicBezier(0.22, 1, 0.36, 1);

// ── Animation helpers ─────────────────────────────────────
// Single reduced-motion choke point: every animation in this file
// funnels through play(), so users who asked for reduced motion get
// instant state changes instead of tweens (CSS can't stop WAAPI
// animations — the guard has to live in JS).
const prefersReduced = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const countUp = (el) => {
    const target = Number(el.dataset.countUp);

    if (!Number.isFinite(target)) {
        return;
    }

    const duration = 900;
    const start = performance.now();

    const frame = (now) => {
        const t = Math.min((now - start) / duration, 1);

        el.textContent = Math.round(target * easeOutShared(t));

        if (t < 1) {
            requestAnimationFrame(frame);
        }
    };

    requestAnimationFrame(frame);
};

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

// Serving scaler on recipe detail: base amounts × chosen serving count.
Alpine.data('servings', (coffee, water) => ({
    n: 1,
    coffee,
    water,
}));

// Horizontal catalog rail: arrows page by one card and disable at the
// ends. Native scrolling stays the source of truth, so trackpad and
// keyboard users need nothing from this component.
Alpine.data('catalogRail', () => ({
    atStart: true,
    atEnd: false,
    canScroll: false,

    init() {
        this.update();
    },

    update() {
        const el = this.$refs.rail;

        if (!el) {
            return;
        }

        const max = el.scrollWidth - el.clientWidth;
        this.canScroll = max > 2;
        this.atStart = el.scrollLeft <= 2;
        this.atEnd = el.scrollLeft >= max - 2;
    },

    scroll(direction) {
        const el = this.$refs.rail;
        const card = el?.firstElementChild;

        if (!el) {
            return;
        }

        const gap = 24;
        const step = card ? card.getBoundingClientRect().width + gap : el.clientWidth * 0.8;

        el.scrollBy({
            left: direction * step,
            behavior: prefersReduced() ? 'auto' : 'smooth',
        });
    },
}));

// Full-screen brew mode: one recipe step at a time plus a countdown of
// the recipe's total time. The data model has no per-step durations, so
// there are no per-step timers. Native <dialog> handles the focus trap
// and Escape; the interval is cleared on close.
Alpine.data('brewMode', (steps = [], minutes = 0) => ({
    steps,
    step: 0,
    total: Math.max(0, Math.round(minutes * 60)),
    left: 0,
    ticker: null,

    get clock() {
        const minutes = String(Math.floor(this.left / 60)).padStart(2, '0');
        const seconds = String(this.left % 60).padStart(2, '0');

        return minutes + ':' + seconds;
    },

    openMode() {
        this.step = 0;
        this.left = this.total;
        document.documentElement.classList.add('is-brewing');
        this.$refs.dialog?.showModal();
        this.$nextTick(() => this.$refs.closeBtn?.focus());
    },

    closeMode() {
        this.$refs.dialog?.close();
    },

    cleanup() {
        this.stop();
        document.documentElement.classList.remove('is-brewing');
    },

    toggleTimer() {
        if (this.ticker) {
            this.stop();
            return;
        }

        if (this.left <= 0) {
            this.left = this.total;
        }

        this.ticker = setInterval(() => {
            this.left = Math.max(0, this.left - 1);

            if (this.left === 0) {
                this.stop();
            }
        }, 1000);
    },

    stop() {
        if (this.ticker) {
            clearInterval(this.ticker);
            this.ticker = null;
        }
    },
}));

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

        // Roll: the current card drops 8px and fades (250ms); the new one
        // arrives from 8px below and rises into place (250ms). No dice
        // spin, no scale.
        if (card && this.recipe) {
            await play(card, { opacity: 0, translateY: 8, duration: 250, ease: easeOut });
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

            // The JSON route has no session, so the current page locale has
            // to travel with the request for translated step texts.
            params.set('locale', window.APP_LOCALE ?? document.documentElement.lang);

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

            await play(card, { opacity: [0, 1], translateY: [8, 0], duration: 250, ease: easeOut });

            // Numbered steps cascade in 50ms apart once the card lands.
            play(card.querySelectorAll('ol li'), {
                opacity: [0, 1],
                translateY: [6, 0],
                duration: 300,
                delay: stagger(50),
                ease: easeOut,
            });
        }
    },

    rollNext() {
        this.rolled = true;
        this.roll(this.recipe?.id ?? null);
    },
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

    // Step transition: current question slides out 24px in direction of
    // travel and fades (200ms), next slides in from opposite side (250ms).
    // Card min-height keeps the footer stable. Under reduced motion play()
    // returns null and each await resolves instantly.
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
                translateX: dir > 0 ? -24 : 24,
                duration: 200,
                ease: easeOut,
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
                    translateX: toEl === fromEl ? [0, 0] : [dir > 0 ? 24 : -24, 0],
                    duration: toEl === fromEl ? 200 : 250,
                    ease: easeOut,
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

            // Same explicit locale as the random endpoint: the API group
            // has no session, so the page language must be sent along.
            params.set('locale', window.APP_LOCALE ?? document.documentElement.lang);

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
            play(this.$refs.results, { opacity: [0, 1], translateY: [16, 0], duration: 350, ease: easeOut });
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

// ── Flowing-water reveal (x-reveal) ───────────────────────
// One-shot fade + 12px lift when an element first enters the
// viewport. Pure CSS transition (app.css) — the directive only
// flips .is-revealed: no anime.js, no inline styles. The hidden
// state lives on a server-rendered `class="reveal"` (so nothing
// flashes) and gates on html.js-motion, set inline in <head>.
// One shared observer mirrors the data-reveal system below, and
// unobserves after the first intersection so the reveal never
// replays. Without IntersectionObserver the element keeps its
// end state and simply renders visible.
const revealObserver = 'IntersectionObserver' in window
    ? new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            entry.target.classList.add('is-revealed');
            revealObserver.unobserve(entry.target);
        });
    }, { threshold: 0, rootMargin: '0px 0px -60px 0px' })
    : null;

// x-reveal also appears on server-rendered cards that sit outside any
// x-data root (recipes, catalog block, brew methods). Alpine.start()
// only initialises root selectors, so without this the directive never
// runs and those cards stay at .reveal's opacity: 0.
Alpine.addInitSelector(() => '[x-reveal]');

Alpine.directive('reveal', (el) => {
    if (!revealObserver) {
        el.classList.add('is-revealed');
        return;
    }

    revealObserver.observe(el);
});

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
                        ease: easeOut,
                    }
                    : {
                        // Explicit from-values: the .in-view class flip above
                        // already set CSS to the hidden state, so a to-only
                        // value would animate 0 → 0 (invisible).
                        opacity: [1, 0],
                        translateY: [0, 24],
                        duration: 450,
                        delay: staggered ? stagger(30) : 0,
                        ease: easeOut,
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

    // Decorative sprigs: one 2° sway when a side branch enters the
    // viewport, around its edge end. CSS owns the tween; this only flips
    // the class once. The resting angle is already the no-JS /
    // reduced-motion state. The 0x0 anchor sits outside the viewport, so
    // observe the art (its box crosses the edge) and settle the anchor.
    const sprigs = document.querySelectorAll('.sprig-side');

    if ('IntersectionObserver' in window && sprigs.length && !prefersReduced()) {
        const sprigObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.closest('.sprig-side')?.classList.add('is-settled');
                sprigObserver.unobserve(entry.target);
            });
        }, {
            threshold: 0,
            rootMargin: '0px 0px -40px 0px',
        });

        sprigs.forEach((el) => sprigObserver.observe(el.querySelector('.sprig-side__art') || el));
    }

    // Hero tilt: mouse only (fine pointer), max 3 degrees per axis, and
    // off for reduced motion. This is direct pointer feedback like a
    // hover state, not a loop; the cup's one-time entrance already ran.
    const tiltZone = document.querySelector('[data-tilt]');

    if (tiltZone && window.matchMedia('(hover: hover) and (pointer: fine)').matches && !prefersReduced()) {
        const art = tiltZone.querySelector('.hero-art');
        const max = 3;

        if (art) {
            tiltZone.addEventListener('pointermove', (e) => {
                const box = tiltZone.getBoundingClientRect();
                const x = (e.clientX - box.left) / box.width - 0.5;
                const y = (e.clientY - box.top) / box.height - 0.5;

                art.style.transform = 'perspective(900px) rotateY('
                    + (x * max * 2).toFixed(2) + 'deg) rotateX('
                    + (-y * max * 2).toFixed(2) + 'deg)';
            });

            tiltZone.addEventListener('pointerleave', () => {
                art.style.transform = '';
            });
        }
    }

    // Pause the cafe map's fetch wave while its container is offscreen.
    // The hero waves are scroll-linked CSS now, and the marquee's
    // animation is paused by the same observer contract. Reduced motion
    // is handled globally in app.css (animation: none), not here.
    const loopEls = document.querySelectorAll('[data-loop]');

    if ('IntersectionObserver' in window && loopEls.length) {
        const loopObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                entry.target.classList.toggle('is-paused', !entry.isIntersecting);
            });
        });

        loopEls.forEach((el) => loopObserver.observe(el));
    }

    // Stats strip: count the totals up from zero once, on entry.
    // Server-rendered numbers stay the no-JS / reduced-motion state, and
    // the strip's data-reveal-stagger wrapper is hidden until it enters,
    // so zeroing here never flashes. tabular-nums holds the width steady.
    const countEls = document.querySelectorAll('[data-count-up]');

    if ('IntersectionObserver' in window && countEls.length && !prefersReduced()) {
        const countObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                countObserver.unobserve(entry.target);
                countUp(entry.target);
            });
        });

        countEls.forEach((el) => {
            el.textContent = '0';
            countObserver.observe(el);
        });
    }

    // Filter chips: move the shared dark pill onto the active chip.
    // CSS cannot measure chip widths, so this runs once on load (and on
    // resize / font load). The pill is the named element of the
    // cross-document view transition, so a filter change slides it.
    // Without JS the active chip keeps its own background.
    document.querySelectorAll('[data-filter-pills]').forEach((row) => {
        const pill = row.querySelector('[data-filter-pill]');
        const active = row.querySelector('[aria-current="page"]');

        if (!pill || !active) {
            return;
        }

        const place = () => {
            const rowBox = row.getBoundingClientRect();
            const chipBox = active.getBoundingClientRect();

            // Content coordinates, not viewport ones: the pill is an
            // absolutely positioned child of the scrollable row, so it rides
            // along with scrollLeft when the mobile one-line row scrolls.
            // Without the offset a deep-linked filter drags the pill offscreen.
            pill.style.width = chipBox.width + 'px';
            pill.style.height = chipBox.height + 'px';
            pill.style.transform = 'translate('
                + (chipBox.left - rowBox.left + row.scrollLeft) + 'px, '
                + (chipBox.top - rowBox.top + row.scrollTop) + 'px)';
            row.classList.add('pill-ready');

            // On the mobile one-line scroller the active filter can start
            // offscreen (deep link): bring it into view without moving the
            // page. Desktop rows are overflow-visible, so this is a no-op.
            if (row.scrollWidth > row.clientWidth + 1
                && (chipBox.left < rowBox.left || chipBox.right > rowBox.right)) {
                row.scrollLeft += chipBox.left - rowBox.left - 16;
            }
        };

        place();
        row.addEventListener('scroll', place, { passive: true });
        window.addEventListener('resize', place);
        document.fonts?.ready.then(place);
    });

    // Mobile bean carousel: drop the right-edge fade once the end is in
    // view, so it only advertises cards that are actually still hidden.
    // Same measurement decides whether the strip is a keyboard-scrollable
    // region: tabindex only while it actually overflows (it is a plain
    // grid from md up, where an extra tab stop would be noise).
    document.querySelectorAll('[data-scroll-fade]').forEach((el) => {
        let queued = false;

        const update = () => {
            queued = false;
            el.classList.toggle('is-end', el.scrollLeft + el.clientWidth >= el.scrollWidth - 2);

            if (el.scrollWidth > el.clientWidth + 1) {
                el.tabIndex = 0;
            } else {
                el.removeAttribute('tabindex');
            }
        };

        const queueUpdate = () => {
            if (!queued) {
                queued = true;
                requestAnimationFrame(update);
            }
        };

        update();
        el.addEventListener('scroll', queueUpdate, { passive: true });
        window.addEventListener('resize', queueUpdate);
    });

    // Recipes: the result grid is server-rendered, so a filter or page
    // change is a document load. Fade the outgoing grid for 120ms, then
    // navigate. Page arrows also stash the direction; the inline head
    // script paints the incoming grid 16px off before first paint, and
    // this adds the settle class so it slides home (250ms).
    const recipesGrid = document.querySelector('[data-recipes-grid]');

    if (recipesGrid) {
        const leave = (href, dir) => {
            if (prefersReduced()) {
                window.location.href = href;
                return;
            }

            if (dir) {
                sessionStorage.setItem('recipes-page-dir', dir);
            }

            recipesGrid.classList.add('is-leaving');
            window.setTimeout(() => { window.location.href = href; }, 120);
        };

        const plainClick = (e) => e.button === 0 && !e.metaKey && !e.ctrlKey && !e.shiftKey && !e.altKey;

        document.querySelectorAll('[data-recipe-filters] a').forEach((link) => {
            link.addEventListener('click', (e) => {
                if (!plainClick(e)) {
                    return;
                }

                e.preventDefault();
                leave(link.href);
            });
        });

        document.querySelectorAll('[data-recipe-pagination] a[rel]').forEach((link) => {
            link.addEventListener('click', (e) => {
                if (!plainClick(e)) {
                    return;
                }

                e.preventDefault();
                leave(link.href, link.rel === 'next' ? 'next' : 'prev');
            });
        });

        // Let the pre-paint offset commit, then settle it. The cleanup
        // restores the neutral state so a later filter fade is not
        // overridden by a stale html class.
        if (/page-enter-(next|prev)/.test(document.documentElement.className)) {
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    document.documentElement.classList.add('grid-settled');

                    window.setTimeout(() => {
                        document.documentElement.classList.remove('page-enter-next', 'page-enter-prev', 'grid-settled');
                    }, 300);
                });
            });
        }
    }
});
