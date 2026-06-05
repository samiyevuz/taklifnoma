@extends('layouts.premium')

@section('body')
    <div class="landing-page relative min-h-dvh pt-24 pb-16">
        <x-landing.ambient />
        <div class="relative z-10 landing-container max-w-3xl">
            <div class="mb-6 flex justify-end"><x-ui.locale-switcher /></div>
            <div class="mb-8 text-center">
                <p class="section-label mb-3 justify-center">Builder</p>
                <h1 class="font-serif text-display font-semibold text-ink">{{ __('builder.create_title') }}</h1>
                <p class="mt-3 text-ink-soft">{{ __('builder.create_subtitle') }}</p>
            </div>
            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50/80 p-4 text-sm text-red-800">
                    <ul class="list-disc pl-5 space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            <x-builder.form :defaults="$defaults" :action="route('builder.store')" />
        </div>
    </div>
@endsection
