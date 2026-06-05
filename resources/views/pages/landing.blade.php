@extends('layouts.premium')

@section('body')
    <div id="main-content" class="landing-page relative min-h-dvh w-full max-w-[100vw]">
        <x-landing.ambient />

        <div class="relative z-10">
            <x-landing.navbar />
            <x-landing.hero />
            <x-landing.templates-grid />
            <x-landing.features />
            <x-landing.testimonials />
            <x-landing.faq />
            <x-landing.pricing-cta />
            <x-landing.footer />
        </div>
    </div>
@endsection
