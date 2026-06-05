@extends('layouts.premium')

@section('body')
    <section class="landing-container py-24 text-center">
        <div class="mx-auto max-w-lg glass-luxury rounded-3xl p-10">
            @if ($isPaid)
                <p class="section-label mb-3 justify-center">{{ __('builder.payment_success_label') }}</p>
                <h1 class="font-serif text-display font-semibold text-ink">{{ __('builder.payment_success_title') }}</h1>
                <p class="mt-4 text-ink-soft">{{ __('builder.payment_success_desc') }}</p>
                @if ($publicUrl)
                    <a href="{{ $publicUrl }}" class="btn-gold-shimmer btn-shine mt-8 inline-flex">{{ __('builder.payment_view_link') }}</a>
                @endif
            @else
                <p class="section-label mb-3 justify-center">{{ __('builder.payment_pending_label') }}</p>
                <h1 class="font-serif text-display font-semibold text-ink">{{ __('builder.payment_pending_title') }}</h1>
                <p class="mt-4 text-ink-soft">{{ __('builder.payment_pending_desc') }}</p>
            @endif
            <a href="{{ auth()->check() ? route('account.dashboard') : '/' }}" class="btn-outline-luxury mt-4 inline-flex">{{ __('builder.back_dashboard') }}</a>
        </div>
    </section>
@endsection
