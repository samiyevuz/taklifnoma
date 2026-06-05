@php
    use App\Support\TemplateCatalog;

    $templates = TemplateCatalog::all();
    $favoriteSlugs = $favoriteSlugs ?? (auth()->check() ? auth()->user()->favoriteSlugs() : []);
@endphp

<section id="shablonlar" class="relative py-16 sm:py-20 lg:py-28" aria-labelledby="templates-heading">
    <div class="landing-container">
        <div class="reveal mx-auto max-w-2xl text-center">
            <p class="section-label mb-4 justify-center">{{ __('landing.templates_label') }}</p>
            <h2 id="templates-heading" class="font-serif text-display font-semibold text-ink text-balance">{{ __('landing.templates_title') }}</h2>
            <p class="mt-4 text-fluid-body text-ink-soft text-pretty">{{ __('landing.templates_desc') }}</p>
        </div>

        <div class="mt-12 grid grid-cols-1 gap-6 min-[400px]:grid-cols-2 lg:grid-cols-4 lg:gap-6">
            @foreach ($templates as $index => $template)
                @php
                    $isFavorited = in_array($template['slug'], $favoriteSlugs, true);
                    $previewUrl = $template['preview_route'] && $template['preview_param']
                        ? route($template['preview_route'], $template['preview_param'])
                        : (auth()->check() ? route('builder.create') : route('login'));
                @endphp
                <article
                    class="template-card reveal {{ $index > 0 ? 'reveal-delay-' . min($index, 4) : '' }}"
                    aria-label="{{ $template['title'] }} shabloni"
                >
                    <div class="template-card__visual relative">
                        <button
                            type="button"
                            class="favorite-btn {{ $isFavorited ? 'is-active' : '' }}"
                            data-favorite-btn
                            data-template-slug="{{ $template['slug'] }}"
                            data-login-url="{{ route('login') }}"
                            data-auth="{{ auth()->check() ? '1' : '0' }}"
                            aria-label="{{ $isFavorited ? __('account.remove_favorite') : __('account.add_favorite') }}"
                            aria-pressed="{{ $isFavorited ? 'true' : 'false' }}"
                        >
                            <svg viewBox="0 0 24 24" fill="{{ $isFavorited ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>

                        <div class="template-card__visual-inner {{ $template['visual'] }}">
                            <div class="template-card__pattern"></div>
                            <div class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center">
                                <div class="mb-3 h-12 w-12 rounded-full border border-white/30"></div>
                                <p class="font-serif text-lg font-medium text-white drop-shadow-md">{{ $template['title'] }}</p>
                                <p class="mt-1 text-xs tracking-widest text-white/70 uppercase">Taklifnoma.uz</p>
                            </div>
                        </div>

                        @if ($template['tag'])
                            <span class="template-card__badge">{{ $template['tag'] }}</span>
                        @endif

                        <div class="template-card__overlay">
                            <p class="font-serif text-lg font-medium text-white">{{ $template['title'] }}</p>
                            <p class="mt-1 text-sm font-semibold text-luxury-gold-light">{{ $template['price'] }}</p>
                        </div>

                        <div class="template-card__quick">
                            <a
                                href="{{ $previewUrl }}"
                                class="inline-flex items-center gap-2 rounded-full bg-white/95 px-5 py-2.5 text-sm font-semibold text-ink shadow-lg transition-transform duration-300 hover:scale-105"
                                style="transition-timing-function: cubic-bezier(0.16, 1, 0.3, 1)"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                {{ __('landing.quick_preview') }}
                            </a>
                        </div>
                    </div>

                    <div class="template-card__body">
                        <h3 class="font-serif text-lg font-semibold text-ink">{{ $template['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-ink-muted line-clamp-2">{{ $template['desc'] }}</p>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-sm font-bold text-luxury-gold-dark">{{ $template['price'] }}</span>
                            <a href="{{ auth()->check() ? route('builder.create') : route('login') }}" class="text-sm font-semibold text-ink hover:text-luxury-gold-dark transition-colors" style="transition-timing-function: cubic-bezier(0.16, 1, 0.3, 1)">
                                {{ __('landing.choose') }} →
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
