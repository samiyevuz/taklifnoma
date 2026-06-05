@extends('layouts.premium')

@section('body')
    <div class="landing-page relative min-h-dvh pt-24 pb-16">
        <x-landing.ambient />
        <div class="relative z-10 landing-container max-w-7xl">
            @if (session('success'))
                <div class="mb-6 rounded-xl border border-luxury-emerald/30 bg-luxury-emerald/10 p-4 text-sm text-luxury-emerald">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-300/40 bg-red-50 p-4 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="admin-shell">
                <x-admin.sidebar />
                <main class="min-w-0">
                    @yield('admin')
                </main>
            </div>
        </div>
    </div>
@endsection

@push('head')
    @vite(['resources/js/admin.js'])
@endpush
