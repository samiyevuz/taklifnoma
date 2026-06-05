@php
    use App\Support\SiteContent;

    $social = collect([
        'instagram' => [
            'url' => SiteContent::contact('instagram'),
            'label' => 'Instagram',
            'icon' => '<path d="M7 2h10a5 5 0 015 5v10a5 5 0 01-5 5H7a5 5 0 01-5-5V7a5 5 0 015-5zm5 5a5 5 0 100 10 5 5 0 000-10zm6.5-.9a1.1 1.1 0 11-2.2 0 1.1 1.1 0 012.2 0z"/>',
        ],
        'telegram' => [
            'url' => SiteContent::contact('telegram'),
            'label' => 'Telegram',
            'icon' => '<path d="M21.5 4.5L3.8 11.2c-1.2.5-1.2 1.2-.2 1.5l4.5 1.4 1.7 5.3c.2.6.8.8 1.2.3l2.4-2.5 4.7 3.5c.6.4 1 .2 1.2-.7l2.8-13.2c.2-.9-.3-1.3-1.2-1.1z"/>',
        ],
        'youtube' => [
            'url' => SiteContent::contact('youtube'),
            'label' => 'YouTube',
            'icon' => '<path d="M21.6 7.2a2.5 2.5 0 00-1.8-1.8C17.8 5 12 5 12 5s-5.8 0-7.8.4A2.5 2.5 0 002.4 7.2 26 26 0 002 12a26 26 0 00.4 4.8 2.5 2.5 0 001.8 1.8C6.2 19 12 19 12 19s5.8 0 7.8-.4a2.5 2.5 0 001.8-1.8A26 26 0 0022 12a26 26 0 00-.4-4.8zM10 15.5V8.5l5.5 3.5L10 15.5z"/>',
        ],
        'facebook' => [
            'url' => SiteContent::contact('facebook'),
            'label' => 'Facebook',
            'icon' => '<path d="M14 8h3V5h-3c-2.8 0-5 2.2-5 5v2H6v3h3v7h3v-7h2.6l.4-3H12V9c0-.6.4-1 1-1z"/>',
        ],
        'whatsapp' => [
            'url' => SiteContent::contact('whatsapp'),
            'label' => 'WhatsApp',
            'icon' => '<path d="M12 3a9 9 0 00-7.8 13.5L3 21l4.6-1.2A9 9 0 1012 3zm0 2a7 7 0 017 7c0 1.2-.3 2.4-.9 3.4l-.3.5.2.6.8 2.1-2.2-.6-.6.2-.5-.3A7 7 0 0112 5zm-2.2 3.4c-.1 0-.3 0-.4.2-.2.2-.7.7-.7 1.7 0 1 .7 2 0.8 2.1.1.2 1.4 2.2 3.4 3 1.7.7 2 .6 2.4.6.4 0 1.3-.5 1.5-1 .2-.5.2-.9.1-1 0-.1-.2-.1-.5-.2-.3-.2-1.5-.7-1.7-.8-.2 0-.4-.1-.6.1-.2.2-.7.8-.9 1-.2.2-.3.2-.6.1-1.5-.7-2.5-2-2.6-2.1-.2-.2 0-.3.1-.5l.4-.5c.1-.1.1-.2.2-.3 0-.1 0-.2 0-.3 0-.1-.5-1.3-.7-1.7-.2-.4-.4-.4-.6-.4z"/>',
        ],
    ])->filter(fn ($item) => filled($item['url']));
@endphp

<footer class="landing-footer" role="contentinfo">
    <div class="landing-container">
        <div class="landing-footer__card glass-luxury">
            <div class="landing-footer__grid">
                <div class="landing-footer__brand">
                    <a href="/" class="landing-footer__logo">
                        <span class="landing-footer__logo-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M12 2L4 8v14h16V8L12 2z" stroke-linejoin="round"/>
                                <path d="M12 8v8M9 11h6" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <span>
                            <span class="landing-footer__logo-title">Taklifnoma</span>
                            <span class="landing-footer__logo-sub">Premium</span>
                        </span>
                    </a>
                    <p class="landing-footer__tagline">{{ __('landing.footer_tagline') }}</p>

                    @if ($social->isNotEmpty())
                        <div class="landing-footer__social" aria-label="{{ __('landing.footer_social') }}">
                            @foreach ($social as $network => $item)
                                <a
                                    href="{{ $item['url'] }}"
                                    class="landing-footer__social-link"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    aria-label="{{ $item['label'] }}"
                                >
                                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">{!! $item['icon'] !!}</svg>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="landing-footer__col">
                    <h3 class="landing-footer__heading">{{ __('landing.footer_platform') }}</h3>
                    <ul class="landing-footer__links">
                        <li><a href="#shablonlar">{{ __('nav.templates') }}</a></li>
                        <li><a href="#xizmatlar">{{ __('nav.services') }}</a></li>
                        <li><a href="#narxlar">{{ __('nav.pricing') }}</a></li>
                        <li><a href="#haqida">{{ __('nav.about') }}</a></li>
                        <li><a href="#savollar">{{ __('landing.footer_faq') }}</a></li>
                    </ul>
                </div>

                <div class="landing-footer__col">
                    <h3 class="landing-footer__heading">{{ __('landing.footer_contact') }}</h3>
                    <ul class="landing-footer__contact">
                        @if (SiteContent::contactFilled('email'))
                            <li>
                                <a href="mailto:{{ SiteContent::contact('email') }}">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    {{ SiteContent::contact('email') }}
                                </a>
                            </li>
                        @endif
                        @if (SiteContent::contactFilled('phone'))
                            <li>
                                <a href="tel:{{ preg_replace('/\s+/', '', SiteContent::contact('phone')) }}">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    {{ SiteContent::contact('phone') }}
                                </a>
                            </li>
                        @endif
                        @if (SiteContent::contactFilled('telegram'))
                            <li>
                                <a href="{{ SiteContent::contact('telegram') }}" target="_blank" rel="noopener noreferrer">
                                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21.5 4.5L3.8 11.2c-1.2.5-1.2 1.2-.2 1.5l4.5 1.4 1.7 5.3c.2.6.8.8 1.2.3l2.4-2.5 4.7 3.5c.6.4 1 .2 1.2-.7l2.8-13.2c.2-.9-.3-1.3-1.2-1.1z"/></svg>
                                    Telegram
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="landing-footer__bottom">
                <p class="landing-footer__copy">
                    &copy; {{ date('Y') }} Taklifnoma. {{ __('landing.footer_rights') }}
                </p>
                <div class="landing-footer__legal">
                    <a href="#">{{ __('landing.footer_privacy') }}</a>
                    <span aria-hidden="true">·</span>
                    <a href="#">{{ __('landing.footer_terms') }}</a>
                </div>
            </div>
        </div>
    </div>
</footer>
