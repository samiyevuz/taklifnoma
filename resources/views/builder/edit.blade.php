@extends('layouts.builder')

@section('body')
    <div class="builder-page relative min-h-dvh">
        <x-landing.ambient />
        <main id="builder-main" class="relative z-10">
            <x-builder.studio :bootstrap="$bootstrap" :invitation="$invitation" :stats="$stats" />
        </main>
    </div>
@endsection
