@extends('layouts.premium')

@section('body')
    <div class="landing-page relative min-h-dvh pt-24 pb-16">
        <x-landing.ambient />
        <div class="relative z-10 landing-container max-w-3xl">
            <div class="mb-6 flex justify-end"><x-ui.locale-switcher /></div>
            @if (session('success'))
                <div class="mb-6 rounded-xl border border-luxury-emerald/30 bg-luxury-emerald/10 p-4 text-sm text-luxury-emerald">{{ session('success') }}</div>
            @endif
            <div class="mb-8">
                <p class="section-label mb-3">Builder</p>
                <h1 class="font-serif text-display font-semibold text-ink">{{ $invitation->coupleTitle() }}</h1>
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $invitation->isPublished() ? 'bg-luxury-emerald/15 text-luxury-emerald' : 'bg-ink/10 text-ink-muted' }}">
                        {{ $invitation->isPublished() ? __('builder.edit_published') : __('builder.edit_draft') }}
                    </span>
                    @if ($invitation->isPublished())
                        <a href="{{ route('invitation.show', $invitation->slug) }}" target="_blank" class="text-sm font-semibold text-luxury-gold-dark hover:underline">{{ __('builder.edit_view') }} → /i/{{ $invitation->slug }}</a>
                    @endif
                </div>
            </div>
            <div class="mb-8 grid grid-cols-3 gap-3">
                <div class="glass-luxury rounded-xl p-4 text-center"><p class="font-serif text-2xl font-bold text-ink">{{ $stats['attending'] }}</p><p class="text-xs text-ink-muted mt-1">{{ __('builder.stats_attending') }}</p></div>
                <div class="glass-luxury rounded-xl p-4 text-center"><p class="font-serif text-2xl font-bold text-ink">{{ $stats['declined'] }}</p><p class="text-xs text-ink-muted mt-1">{{ __('builder.stats_declined') }}</p></div>
                <div class="glass-luxury rounded-xl p-4 text-center"><p class="font-serif text-2xl font-bold text-ink">{{ $stats['total_guests'] }}</p><p class="text-xs text-ink-muted mt-1">{{ __('builder.stats_guests') }}</p></div>
            </div>
            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50/80 p-4 text-sm text-red-800">
                    <ul class="list-disc pl-5 space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            <x-builder.form :invitation="$invitation" :action="route('builder.update', $invitation)" method="PUT" />
        </div>
    </div>
@endsection
