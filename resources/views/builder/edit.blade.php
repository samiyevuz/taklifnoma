@extends('layouts.builder')

@section('body')
    <main id="builder-main">
        <x-builder.studio :bootstrap="$bootstrap" :invitation="$invitation" :stats="$stats" />
    </main>
@endsection
