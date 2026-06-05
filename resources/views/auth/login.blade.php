@extends('layouts.premium')

@section('body')
    <div class="landing-page relative min-h-dvh flex items-center justify-center pt-24 pb-16 px-4">
        <x-landing.ambient />
        <div class="relative z-10 w-full max-w-md">
            <div class="mb-6 flex justify-center"><x-ui.locale-switcher /></div>
            <div class="auth-card glass-luxury">
                <div class="text-center mb-8">
                    <p class="section-label mb-3 justify-center">{{ __('auth.account_section') }}</p>
                    <h1 class="font-serif text-2xl font-semibold text-ink">{{ __('auth.login_title') }}</h1>
                    <p class="mt-2 text-sm text-ink-soft">{{ __('auth.login_subtitle') }}</p>
                </div>
                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-red-200 bg-red-50/80 p-4 text-sm text-red-800">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ url('/login') }}" class="space-y-5">
                    @csrf
                    <div class="auth-field">
                        <label for="email">{{ __('auth.email') }}</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
                    </div>
                    <div class="auth-field">
                        <label for="password">{{ __('auth.password') }}</label>
                        <input type="password" id="password" name="password" required autocomplete="current-password">
                    </div>
                    <label class="flex items-center gap-2 text-sm text-ink-soft cursor-pointer">
                        <input type="checkbox" name="remember" value="1" class="rounded" {{ old('remember') ? 'checked' : '' }}>
                        {{ __('auth.remember') }}
                    </label>
                    <button type="submit" class="btn-gold-shimmer btn-shine w-full" data-ripple>{{ __('auth.submit_login') }}</button>
                </form>
                <p class="mt-6 text-center text-sm text-ink-muted">
                    {{ __('auth.no_account') }}
                    <a href="{{ route('register') }}" class="font-semibold text-luxury-gold-dark hover:underline">{{ __('auth.register_title') }}</a>
                </p>
            </div>
        </div>
    </div>
@endsection
