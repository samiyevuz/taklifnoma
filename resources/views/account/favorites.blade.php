@extends('layouts.account')

@section('account')
    <div class="mb-8">
        <p class="section-label mb-3">Sevimlilar</p>
        <h1 class="font-serif text-display font-semibold text-ink">Yoqtirganlar</h1>
        <p class="mt-2 text-ink-soft">Saqlangan premium shablonlaringiz.</p>
    </div>

    @if (count($templates))
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            @foreach ($templates as $template)
                <article class="account-card glass-luxury relative">
                    <div class="template-card__visual {{ $template['visual'] }} rounded-xl h-36 mb-4"></div>
                    <h3 class="font-serif text-lg font-semibold text-ink">{{ $template['title'] }}</h3>
                    <p class="mt-2 text-sm text-ink-muted line-clamp-2">{{ $template['desc'] }}</p>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="text-sm font-bold text-luxury-gold-dark">{{ $template['price'] }}</span>
                        <div class="flex items-center gap-3">
                            <form action="{{ route('favorites.destroy', $template['slug']) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-ink-muted hover:text-red-600">Olib tashlash</button>
                            </form>
                            <a href="{{ route('builder.create') }}" class="text-sm font-semibold text-ink hover:text-luxury-gold-dark">Yaratish →</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="account-card glass-luxury text-center py-14">
            <p class="text-ink-soft">Hali yoqtirgan shablonlar yo'q.</p>
            <a href="/#shablonlar" class="btn-outline-luxury mt-4 inline-flex">Shablonlarni ko'rish</a>
        </div>
    @endif
@endsection
