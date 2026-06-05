@extends('layouts.builder')

@section('body')
    <main id="builder-main">
        <x-builder.studio :bootstrap="$bootstrap" />
    </main>
@endsection
