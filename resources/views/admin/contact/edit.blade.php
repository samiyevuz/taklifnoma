@extends('layouts.admin')

@section('admin')
    <div class="mb-8">
        <p class="section-label mb-3">{{ __('admin.contact') }}</p>
        <h1 class="font-serif text-display font-semibold text-ink">{{ __('admin.contact_title') }}</h1>
        <p class="mt-2 text-sm text-ink-soft">{{ __('admin.contact_subtitle') }}</p>
    </div>

    <form method="POST" action="{{ route('admin.contact.update') }}" class="admin-form-grid">
        @csrf
        @method('PUT')

        <div class="admin-card glass-luxury">
            <h2 class="admin-card__title">{{ __('admin.sections.contact_direct') }}</h2>

            <div class="admin-form-row">
                <label class="admin-label" for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $contact['email']) }}" class="admin-input w-full" placeholder="info@theuzsoft.uz">
            </div>

            <div class="admin-form-row">
                <label class="admin-label" for="phone">{{ __('admin.table.phone') }}</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $contact['phone']) }}" class="admin-input w-full" placeholder="+998 88 222 22 87">
            </div>
        </div>

        <div class="admin-card glass-luxury">
            <h2 class="admin-card__title">{{ __('admin.sections.social_links') }}</h2>

            @foreach (['instagram' => 'Instagram', 'telegram' => 'Telegram', 'youtube' => 'YouTube', 'facebook' => 'Facebook', 'whatsapp' => 'WhatsApp'] as $key => $label)
                <div class="admin-form-row">
                    <label class="admin-label" for="{{ $key }}">{{ $label }}</label>
                    <input type="url" id="{{ $key }}" name="{{ $key }}" value="{{ old($key, $contact[$key]) }}" class="admin-input w-full" placeholder="https://">
                </div>
            @endforeach

            <p class="admin-hint">{{ __('admin.contact_hint') }}</p>
        </div>

        <div class="admin-form-actions">
            <button type="submit" class="btn-primary-luxury">{{ __('admin.save') }}</button>
        </div>
    </form>
@endsection
