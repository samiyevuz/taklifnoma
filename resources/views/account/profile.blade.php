@extends('layouts.account')

@section('account')
    <div class="mb-8">
        <p class="section-label mb-3">{{ __('account.profile') }}</p>
        <h1 class="font-serif text-display font-semibold text-ink">{{ __('account.profile_title') }}</h1>
        <p class="mt-2 text-ink-soft">{{ __('account.profile_subtitle') }}</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50/80 p-4 text-sm text-red-800">
            <ul class="list-disc pl-5 space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="space-y-6">
        <form action="{{ route('account.profile.update') }}" method="POST" class="account-card glass-luxury space-y-5">
            @csrf @method('PUT')
            <h2 class="font-serif text-xl font-semibold text-ink">{{ __('account.basic_info') }}</h2>
            <div class="auth-field"><label for="name">{{ __('auth.name') }}</label><input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required></div>
            <div class="auth-field"><label for="email">{{ __('auth.email') }}</label><input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required></div>
            <div class="auth-field"><label for="phone">{{ __('auth.phone') }}</label><input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+998 90 123 45 67"></div>
            <button type="submit" class="btn-gold-shimmer btn-shine" data-ripple>{{ __('account.save') }}</button>
        </form>

        <form action="{{ route('account.password.update') }}" method="POST" class="account-card glass-luxury space-y-5">
            @csrf @method('PUT')
            <h2 class="font-serif text-xl font-semibold text-ink">{{ __('account.update_password') }}</h2>
            <div class="auth-field"><label for="current_password">{{ __('account.current_password') }}</label><input type="password" id="current_password" name="current_password" required autocomplete="current-password"></div>
            <div class="auth-field"><label for="password">{{ __('account.new_password') }}</label><input type="password" id="password" name="password" required autocomplete="new-password"></div>
            <div class="auth-field"><label for="password_confirmation">{{ __('account.new_password_confirm') }}</label><input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"></div>
            <button type="submit" class="btn-outline-luxury">{{ __('account.update_password_btn') }}</button>
        </form>
    </div>
@endsection
