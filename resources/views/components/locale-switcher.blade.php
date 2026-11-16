@php
    $current = app()->getLocale();
    $variant = $variant ?? 'desktop';
    $panelId = 'locale-options-' . $variant;
    $languages = ['en' => __('English'), 'id' => __('Bahasa Indonesia')];
    $items = ['en' => 'EN', 'id' => 'ID'];
@endphp

@if ($variant === 'mobile')
    {{-- Inside the hamburger menu there is no globe and no collapse: both
         languages are visible and act as plain links. --}}
    <div class="locale-switch locale-switch--static bg-coffee-latte/10"
         data-active="{{ $current }}" role="group" aria-label="{{ __('Language') }}">
        <div class="locale-switch__track">
            <span class="locale-switch__fill" aria-hidden="true"></span>
            @foreach ($items as $code => $abbr)
                <a href="{{ route('locale.switch', $code) }}"
                   lang="{{ $code }}" hreflang="{{ $code }}"
                   data-locale="{{ $code }}"
                   @if ($current !== $code) title="{{ $code === 'en' ? __('Switch to English') : __('Switch to Indonesian') }}" @endif
                   aria-label="{{ $current === $code ? $languages[$code] : ($code === 'en' ? __('Switch to English') : __('Switch to Indonesian')) }}"
                   @if ($current === $code) aria-current="true" @endif
                   class="locale-switch__link">{{ $abbr }}</a>
            @endforeach
        </div>
    </div>
@else
    <div class="locale-switch bg-coffee-latte/10"
         data-active="{{ $current }}"
         x-data="{
             open: false,
             navigating: false,
             current: '{{ $current }}',
             pending: '{{ $current }}',
             show() {
                 this.open = true;
                 this.$nextTick(() => {
                     const p = this.$refs.panel;
                     (p.querySelector('[aria-current]') || p.querySelector('a')).focus();
                 });
             },
             close(f) {
                 if (!this.open) return;
                 this.open = false;
                 if (f) this.$refs.globe.focus();
             },
             choose(el) {
                 if (this.navigating) return;
                 this.navigating = true;
                 const h = el.href;
                 const r = matchMedia('(prefers-reduced-motion: reduce)').matches;
                 if (el.dataset.locale === this.current) {
                     this.open = false;
                     setTimeout(() => location.href = h, r ? 0 : 200);
                 } else if (r) {
                     location.href = h;
                 } else {
                     this.pending = el.dataset.locale;
                     setTimeout(() => this.open = false, 150);
                     setTimeout(() => location.href = h, 350);
                 }
             },
             move(d) {
                 const l = [...this.$refs.panel.querySelectorAll('a[data-locale]')];
                 const i = l.indexOf(document.activeElement);
                 l[(i + d + l.length) % l.length].focus();
             }
         }"
         :data-active="pending"
         :class="{ 'is-open': open }"
         @click.outside="close(false)"
         @keydown.escape="close(true)">
        <button type="button" x-ref="globe"
                class="locale-switch__globe"
                aria-controls="{{ $panelId }}"
                aria-expanded="false"
                :aria-expanded="open.toString()"
                aria-label="{{ __('Language: :language', ['language' => $languages[$current]]) }}"
                @click="open ? close(true) : show()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="9"/>
                <path d="M3 12h18"/>
                <path d="M12 3a14.5 14.5 0 0 1 0 18a14.5 14.5 0 0 1 0-18"/>
            </svg>
        </button>

        <div id="{{ $panelId }}" x-ref="panel" class="locale-switch__pills"
             role="group" aria-label="{{ __('Language') }}"
             @keydown.arrow-right.prevent="move(1)"
             @keydown.arrow-left.prevent="move(-1)">
            <div class="locale-switch__track">
                <span class="locale-switch__fill" aria-hidden="true"></span>
                @foreach ($items as $code => $abbr)
                    <a href="{{ route('locale.switch', $code) }}"
                       lang="{{ $code }}" hreflang="{{ $code }}"
                       data-locale="{{ $code }}"
                       @if ($current !== $code) title="{{ $code === 'en' ? __('Switch to English') : __('Switch to Indonesian') }}" @endif
                       aria-label="{{ $current === $code ? $languages[$code] : ($code === 'en' ? __('Switch to English') : __('Switch to Indonesian')) }}"
                       @if ($current === $code) aria-current="true" @endif
                       :aria-current="pending === '{{ $code }}' ? 'true' : null"
                       class="locale-switch__link"
                       @click.prevent="choose($el)">{{ $abbr }}</a>
                @endforeach
            </div>
        </div>

        <noscript>
            <style>
                .locale-switch__pills {
                    grid-template-columns: 1fr !important;
                    visibility: visible !important;
                }
                .locale-switch__link { opacity: 1 !important; }
                .locale-switch__globe { display: none !important; }
            </style>
        </noscript>
    </div>
@endif
