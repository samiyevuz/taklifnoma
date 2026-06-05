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

        <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 lg:gap-7">
            @foreach ($templates as $index => $template)
                @php
                    $isFavorited = in_array($template['slug'], $favoriteSlugs, true);
                    $builderUrl = auth()->check()
                        ? route('builder.create', ['template' => $template['slug']])
                        : route('login');
                    $previewUrl = $template['preview_route'] && $template['preview_param']
                        ? route($template['preview_route'], $template['preview_param'])
                        : $builderUrl;
                @endphp
                <article
                    class="template-card reveal {{ $index > 0 ? 'reveal-delay-' . min($index, 6) : '' }}"
                    aria-label="{{ $template['title'] }} shabloni"
                >
                    <div class="template-card__actions">
                        @if ($template['tag'])
                            <span class="template-card__badge">{{ $template['tag'] }}</span>
                        @endif

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
                    </div>

                    <a href="{{ $previewUrl }}" class="template-card__preview-link" aria-label="{{ __('landing.quick_preview') }}: {{ $template['title'] }}">
                        <div class="template-card__hero {{ $template['visual'] }}">
                            @if ($template['cover_url'] ?? null)
                                <img
                                    src="{{ $template['cover_url'] }}"
                                    alt="{{ $template['title'] }}"
                                    class="template-card__cover"
                                    loading="lazy"
                                    decoding="async"
                                >
                            @endif
                            <div class="template-card__photo-overlay" aria-hidden="true"></div>
                            <div class="template-card__hero-content">
                                <div class="template-card__icon-ring">
                                    <x-landing.template-icon :slug="$template['slug']" />
                                </div>
                                <p class="template-card__hero-title">{{ $template['title'] }}</p>
                                <p class="template-card__hero-price">{{ $template['price'] }}</p>
                            </div>
                            <span class="template-card__preview-chip">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                {{ __('landing.quick_preview') }}
                            </span>
                        </div>
                    </a>

                    <div class="template-card__body">
                        <h3 class="font-serif text-lg font-semibold text-ink">{{ $template['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-ink-muted line-clamp-2">{{ $template['desc'] }}</p>
                        <div class="template-card__footer">
                            <span class="template-card__price">{{ $template['price'] }}</span>
                            <a href="{{ $builderUrl }}" class="template-card__cta">
                                {{ __('landing.choose') }}
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
