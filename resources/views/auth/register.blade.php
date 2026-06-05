@extends('layouts.premium')

@section('body')
    <div class="landing-page relative min-h-dvh flex items-center justify-center pt-24 pb-16 px-4">
        <x-landing.ambient />
        <div class="relative z-10 w-full max-w-md">
            <div class="auth-card glass-luxury">
                <div class="text-center mb-8">
                    <p class="section-label mb-3 justify-center">Qo'shilish</p>
                    <h1 class="font-serif text-2xl font-semibold text-ink">Ro'yxatdan o'tish</h1>
                    <p class="mt-2 text-sm text-ink-soft">Premium taklifnoma platformasiga mijoz sifatida qo'shiling.</p>
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

                <form method="POST" action="{{ url('/register') }}" class="space-y-5">
                    @csrf
                    <div class="auth-field">
                        <label for="name">Ism familiya</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                    </div>
                    <div class="auth-field">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                    </div>
                    <div class="auth-field">
                        <label for="phone">Telefon (ixtiyoriy)</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+998 90 123 45 67" autocomplete="tel">
                    </div>
                    <div class="auth-field">
                        <label for="password">Parol</label>
                        <input type="password" id="password" name="password" required autocomplete="new-password">
                    </div>
                    <div class="auth-field">
                        <label for="password_confirmation">Parolni tasdiqlang</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                    </div>
                    <button type="submit" class="btn-gold-shimmer btn-shine w-full" data-ripple>Hisob yaratish</button>
                </form>

                <p class="mt-6 text-center text-sm text-ink-muted">
                    Allaqachon hisobingiz bormi?
                    <a href="{{ route('login') }}" class="font-semibold text-luxury-gold-dark hover:underline">Kirish</a>
                </p>
            </div>
        </div>
    </div>
@endsection
