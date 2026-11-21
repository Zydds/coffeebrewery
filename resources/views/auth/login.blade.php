<x-guest-layout>
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <div class="mb-8">
        <p class="eyebrow">{{ __('Welcome back') }}</p>
        <h1 id="login-title" class="mt-3 font-display text-3xl font-extrabold tracking-tight text-coffee-espresso">{{ __('Sign in to your workspace') }}</h1>
        <p class="mt-3 text-sm leading-6 text-coffee-mocha">{{ __('Use your admin account to continue.') }}</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" class="font-medium text-coffee-espresso" :value="__('Email address')" />
            <x-text-input id="email" class="mt-2 block w-full rounded-xl border-coffee-mocha/40 bg-coffee-latte px-4 py-3 text-coffee-espresso placeholder:text-coffee-mocha/60 focus:border-coffee-amber-ink focus:ring-coffee-amber/30" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" class="font-medium text-coffee-espresso" :value="__('Password')" />
            <x-text-input id="password" class="mt-2 block w-full rounded-xl border-coffee-mocha/40 bg-coffee-latte px-4 py-3 text-coffee-espresso placeholder:text-coffee-mocha/60 focus:border-coffee-amber-ink focus:ring-coffee-amber/30" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label for="remember_me" class="flex items-center gap-3 text-sm text-coffee-mocha">
            <input id="remember_me" type="checkbox" class="rounded border-coffee-mocha/50 text-coffee-amber-ink shadow-none focus:ring-coffee-amber/40" name="remember">
            <span>{{ __('Remember me') }}</span>
        </label>

        <div class="flex flex-wrap items-center justify-between gap-4 pt-2">
            @if (Route::has('password.request'))
                <a class="rounded text-sm font-medium text-coffee-amber-ink underline decoration-coffee-amber/60 underline-offset-4 transition-colors hover:text-coffee-espresso focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="rounded-xl bg-coffee-espresso px-5 py-3 text-sm font-semibold text-coffee-latte shadow-none transition-transform duration-150 ease-out hover:bg-coffee-mocha motion-safe:active:scale-[0.96]">
                {{ __('Sign in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
