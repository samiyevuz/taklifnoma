@extends('layouts.premium')

@section('body')
    <div id="main-content" class="min-h-dvh w-full max-w-[100vw] overflow-x-hidden">
        {{-- Decorative ambient orbs (GPU transform only) --}}
        <div class="pointer-events-none fixed inset-0 overflow-hidden" aria-hidden="true">
            <div
                class="absolute -top-24 -right-16 h-64 w-64 rounded-full bg-luxury-purple/10 blur-3xl gpu-transform animate-float sm:h-80 sm:w-80"
            ></div>
            <div
                class="absolute -bottom-32 -left-20 h-72 w-72 rounded-full bg-luxury-gold/15 blur-3xl gpu-transform animate-float sm:h-96 sm:w-96"
                style="animation-delay: 1.5s"
            ></div>
        </div>

        <div class="relative mx-auto w-full max-w-6xl px-fluid-sm py-fluid-lg sm:px-fluid-md">
            {{-- Header --}}
            <header class="mb-fluid-lg flex flex-wrap items-center justify-between gap-fluid-sm animate-fade-in">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl glass glass-border-gold font-serif text-lg font-semibold text-luxury-gold"
                    >
                        T
                    </div>
                    <span class="font-serif text-lg font-semibold tracking-tight text-royal-900 dark:text-cream-100">
                        Taklifnoma
                    </span>
                </div>

                <button
                    type="button"
                    id="theme-toggle"
                    class="glass rounded-full px-4 py-2 text-sm font-medium text-royal-700 dark:text-cream-200"
                    aria-label="Mavzu rejimini almashtirish"
                    aria-pressed="false"
                >
                    Qorong'u rejim
                </button>
            </header>

            {{-- Hero typography --}}
            <x-ui.typography-demo class="mb-fluid-xl" />

            {{-- UI Kit grid --}}
            <div class="grid grid-cols-1 gap-fluid-md lg:grid-cols-2 lg:gap-fluid-lg">
                <x-ui.glass-card
                    title="Hashamatli taklifnoma"
                    subtitle="Glassmorphism dizayn namunasi"
                    border="gold"
                >
                    <p class="text-pretty">
                        Shaffof shisha effekti, yumshoq yaltiroq chegaralar va premium rang palitrasi —
                        mehmonlaringiz birinchi soniyadan zavqlanadi.
                    </p>

                    <ul class="mt-fluid-sm space-y-2 text-sm text-royal-600 dark:text-royal-500">
                        <li class="flex items-center gap-2">
                            <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-luxury-gold"></span>
                            Mobil-first (320px+)
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-luxury-emerald"></span>
                            GPU tezlashtirilgan animatsiyalar
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-luxury-purple"></span>
                            O'zbek tilida premium matn
                        </li>
                    </ul>

                    <x-slot:footer>
                        <p class="text-xs text-royal-500">
                            Komponent: <code class="text-luxury-gold">x-ui.glass-card</code>
                        </p>
                    </x-slot:footer>
                </x-ui.glass-card>

                <div class="flex flex-col gap-fluid-md">
                    <x-ui.glass-card title="Boshlash" border="purple">
                        <p class="mb-fluid-md">
                            Bir necha bosqichda professional taklifnoma yarating. To'y, tug'ilgan kun yoki
                            korporativ tadbir — barchasi uchun tayyor.
                        </p>

                        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                            <x-ui.cta-button href="#">
                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                Bepul boshlash
                            </x-ui.cta-button>

                            <x-ui.cta-button variant="purple" type="button">
                                Namunalarni ko'rish
                            </x-ui.cta-button>
                        </div>
                    </x-ui.glass-card>

                    <x-ui.glass-card title="Rang palitrasi" border="emerald">
                        <div class="grid grid-cols-3 gap-3 sm:grid-cols-6">
                            @foreach ([
                                ['bg-cream-200', 'Cream'],
                                ['bg-royal-900', 'Royal'],
                                ['bg-luxury-gold', 'Gold'],
                                ['bg-luxury-purple', 'Purple'],
                                ['bg-luxury-emerald', 'Emerald'],
                                ['bg-luxury-gold-light', 'Accent'],
                            ] as [$color, $label])
                                <div class="text-center">
                                    <div class="{{ $color }} mx-auto h-10 w-full max-w-16 rounded-lg shadow-sm ring-1 ring-royal-900/10"></div>
                                    <span class="mt-1 block text-[0.65rem] text-royal-500">{{ $label }}</span>
                                </div>
                            @endforeach
                        </div>
                    </x-ui.glass-card>
                </div>
            </div>

            <footer class="mt-fluid-xl animate-fade-in text-center">
                <p class="text-fluid-body text-royal-500">
                    Premium UI Kit Preview —
                    <span class="font-medium text-luxury-gold">Taklifnoma</span>
                </p>
                <p class="mt-2 font-mono text-xs text-royal-500" id="breakpoint-label">
                    Viewport: <span id="breakpoint-value">—</span>
                </p>
            </footer>
        </div>
    </div>
@endsection
