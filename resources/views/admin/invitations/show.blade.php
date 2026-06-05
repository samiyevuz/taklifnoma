@extends('layouts.admin')

@section('admin')
    <div class="mb-8">
        <a href="{{ route('admin.invitations.index') }}" class="text-sm font-semibold text-luxury-gold-dark hover:underline">← {{ __('admin.invitations') }}</a>
        <h1 class="mt-3 font-serif text-display font-semibold text-ink">{{ $invitation->displayTitle() }}</h1>
        <p class="mt-2 text-ink-soft">{{ $invitation->event_type }} · {{ $invitation->formattedEventDate() }} · {{ $invitation->event_city }}</p>
    </div>

    <div class="admin-grid-2 mb-8">
        <div class="admin-card glass-luxury">
            <h2 class="admin-card__title">Ma'lumotlar</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-ink-muted">{{ __('admin.owner') }}</dt><dd class="font-medium text-ink">{{ $invitation->user?->name ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-ink-muted">{{ __('admin.table.slug') }}</dt><dd><code>/l/{{ $invitation->slug }}</code></dd></div>
                <div class="flex justify-between gap-4"><dt class="text-ink-muted">Shablon</dt><dd>{{ $invitation->template_slug }} @if($invitation->template_variant)<span class="text-ink-muted">/ {{ $invitation->template_variant }}</span>@endif</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-ink-muted">Tarif</dt><dd>{{ ucfirst($invitation->plan_tier ?? '—') }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-ink-muted">Mehmon limiti</dt><dd>{{ $invitation->resolvedGuestLimit() ?? 'Cheksiz' }}</dd></div>
                @if ($invitation->custom_domain)
                    <div class="flex justify-between gap-4"><dt class="text-ink-muted">Maxsus domen</dt><dd><code>{{ $invitation->custom_domain }}</code></dd></div>
                @endif
                <div class="flex justify-between gap-4"><dt class="text-ink-muted">Joy</dt><dd>{{ $invitation->venue_name }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-ink-muted">RSVP</dt><dd>{{ $invitation->rsvp_enabled ? 'Yoqilgan' : 'O\'chirilgan' }}</dd></div>
            </dl>
            <div class="mt-5 flex flex-wrap gap-3">
                @if ($invitation->isPublished())
                    <a href="{{ route('invitation.show', $invitation->slug) }}" target="_blank" class="btn-outline-luxury text-sm">{{ __('admin.view') }}</a>
                @endif
                @if ($invitation->user_id)
                    <a href="{{ route('builder.edit', $invitation) }}" class="btn-outline-luxury text-sm">{{ __('admin.edit') }}</a>
                @endif
            </div>
        </div>

        <div class="admin-card glass-luxury">
            <h2 class="admin-card__title">{{ __('admin.status') }}</h2>
            <form method="POST" action="{{ route('admin.invitations.status', $invitation) }}" class="flex flex-wrap items-end gap-3">
                @csrf
                @method('PATCH')
                <select name="status" class="admin-select">
                    <option value="draft" @selected($invitation->status === 'draft')>{{ __('admin.filter_draft') }}</option>
                    <option value="active" @selected($invitation->status === 'active')>{{ __('admin.filter_active') }}</option>
                    <option value="expired" @selected($invitation->status === 'expired')>{{ __('admin.filter_expired') }}</option>
                </select>
                <button type="submit" class="btn-gold-shimmer btn-shine text-sm">{{ __('admin.save') }}</button>
            </form>
            <form method="POST" action="{{ route('admin.invitations.destroy', $invitation) }}" class="mt-4" onsubmit="return confirm(@json(__('admin.confirm_delete')))">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-outline-luxury text-sm text-red-700 border-red-200">{{ __('admin.delete') }}</button>
            </form>
        </div>
    </div>

    <x-rsvp.live-panel
        :invitation="$invitation"
        :snapshot="$rsvpSnapshot"
        :poll-url="route('builder.rsvp.live', $invitation)"
    />
@endsection
