@extends('layouts.builder')

@section('body')
    <div class="builder-page relative min-h-dvh">
        <x-landing.ambient />
        @if (request()->boolean('complimentary'))
            <div class="landing-container relative z-20 pt-24">
                <div class="rounded-xl border border-luxury-emerald/30 bg-luxury-emerald/10 p-4 text-sm text-luxury-emerald">
                    {{ __('builder.complimentary_success') }}
                </div>
            </div>
        @endif
        <main id="builder-main" class="relative z-10">
            <x-builder.studio :bootstrap="$bootstrap" :invitation="$invitation" :rsvp-snapshot="$rsvpSnapshot" />
        </main>
    </div>
@endsection
