@php($current = app()->getLocale())

<div class="flex items-center gap-0.5 rounded-full bg-coffee-latte/10 p-1 ps-2 {{ $attributes->get('class') }}"
     role="group" aria-label="{{ __('Language') }}">
    <svg class="shrink-0 text-coffee-cream/70" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <circle cx="12" cy="12" r="9"/>
        <path d="M3 12h18"/>
        <path d="M12 3a14.5 14.5 0 0 1 0 18a14.5 14.5 0 0 1 0-18"/>
    </svg>

    @foreach (['en', 'id'] as $code)
        <a href="{{ route('locale.switch', $code) }}"
           lang="{{ $code }}" hreflang="{{ $code }}"
           @if ($current !== $code) title="{{ $code === 'en' ? __('Switch to English') : __('Switch to Indonesian') }}" @endif
           aria-label="{{ $code === 'en' ? __('Switch to English') : __('Switch to Indonesian') }}"
           @if ($current === $code) aria-current="true" @endif
           class="min-h-[36px] px-3 inline-flex items-center rounded-full text-xs font-bold tracking-wide transition-colors {{ $current === $code
                ? 'bg-coffee-amber text-coffee-espresso shadow-xs'
                : 'text-coffee-cream hover:text-coffee-amber' }}">
            {{ strtoupper($code) }}
        </a>
    @endforeach
</div>
