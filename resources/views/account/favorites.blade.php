@extends('layouts.account')

@section('account')
    <div class="mb-8">
        <p class="section-label mb-3">{{ __('account.favorites') }}</p>
        <h1 class="font-serif text-display font-semibold text-ink">{{ __('account.favorites_title') }}</h1>
        <p class="mt-2 text-ink-soft">{{ __('account.favorites_subtitle') }}</p>
    </div>

    @if (count($templates))
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            @foreach ($templates as $template)
                <article class="account-card glass-luxury relative">
                    <div class="rounded-xl h-36 mb-4 overflow-hidden flex {{ $template['visual'] }}">
                        <div class="template-card__scene flex-1">
                            <x-landing.template-icon :slug="$template['slug']" class="template-card__icon h-10 w-10" />
                        </div>
                        <div class="template-card__label flex-1">
                            <p class="template-card__label-title text-sm">{{ $template['title'] }}</p>
                        </div>
                    </div>
                    <h3 class="font-serif text-lg font-semibold text-ink">{{ $template['title'] }}</h3>
                    <p class="mt-2 text-sm text-ink-muted line-clamp-2">{{ $template['desc'] }}</p>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="text-sm font-bold text-luxury-gold-dark">{{ $template['price'] }}</span>
                        <div class="flex items-center gap-3">
                            <form action="{{ route('favorites.destroy', $template['slug']) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-sm text-ink-muted hover:text-red-600">{{ __('account.remove') }}</button>
                            </form>
                            <a href="{{ route('builder.create', ['template' => $template['slug']]) }}" class="text-sm font-semibold text-ink hover:text-luxury-gold-dark">{{ __('account.create') }} →</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="account-card glass-luxury text-center py-14">
            <p class="text-ink-soft">{{ __('account.favorites_empty') }}</p>
            <a href="/#shablonlar" class="btn-outline-luxury mt-4 inline-flex">{{ __('account.browse_templates') }}</a>
        </div>
    @endif
@endsection
