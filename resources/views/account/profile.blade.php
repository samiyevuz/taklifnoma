@extends('layouts.account')

@section('account')
    <div class="mb-8">
        <p class="section-label mb-3">Hisob</p>
        <h1 class="font-serif text-display font-semibold text-ink">Profil</h1>
        <p class="mt-2 text-ink-soft">Shaxsiy ma'lumotlaringiz va xavfsizlik sozlamalari.</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50/80 p-4 text-sm text-red-800">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="space-y-6">
        <form action="{{ route('account.profile.update') }}" method="POST" class="account-card glass-luxury space-y-5">
            @csrf
            @method('PUT')
            <h2 class="font-serif text-xl font-semibold text-ink">Asosiy ma'lumotlar</h2>

            <div class="auth-field">
                <label for="name">Ism familiya</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="auth-field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
            </div>
            <div class="auth-field">
                <label for="phone">Telefon (ixtiyoriy)</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+998 90 123 45 67">
            </div>

            <button type="submit" class="btn-gold-shimmer btn-shine" data-ripple>Saqlash</button>
        </form>

        <form action="{{ route('account.password.update') }}" method="POST" class="account-card glass-luxury space-y-5">
            @csrf
            @method('PUT')
            <h2 class="font-serif text-xl font-semibold text-ink">Parolni yangilash</h2>

            <div class="auth-field">
                <label for="current_password">Joriy parol</label>
                <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
            </div>
            <div class="auth-field">
                <label for="password">Yangi parol</label>
                <input type="password" id="password" name="password" required autocomplete="new-password">
            </div>
            <div class="auth-field">
                <label for="password_confirmation">Yangi parol (tasdiq)</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn-outline-luxury">Parolni yangilash</button>
        </form>
    </div>
@endsection
