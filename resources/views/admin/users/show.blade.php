@extends('layouts.admin')

@section('admin')
    <div class="mb-8">
        <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-luxury-gold-dark hover:underline">← {{ __('admin.users') }}</a>
        <h1 class="mt-3 font-serif text-display font-semibold text-ink">{{ $user->name }}</h1>
        <p class="mt-2 text-ink-soft">{{ $user->email }} · {{ $user->phone ?: '—' }}</p>
    </div>

    <div class="admin-stat-grid mb-8">
        <div class="admin-stat glass-luxury"><p class="admin-stat__value">{{ $user->invitations_count }}</p><p class="admin-stat__label">{{ __('admin.table.invitations') }}</p></div>
        <div class="admin-stat glass-luxury"><p class="admin-stat__value">{{ $user->payment_invoices_count }}</p><p class="admin-stat__label">{{ __('admin.table.payments') }}</p></div>
        <div class="admin-stat glass-luxury"><p class="admin-stat__value">{{ $user->favorites_count }}</p><p class="admin-stat__label">{{ __('admin.table.favorites') }}</p></div>
        <div class="admin-stat glass-luxury"><p class="admin-stat__value">{{ $user->hasTelegramLinked() ? '✓' : '—' }}</p><p class="admin-stat__label">{{ __('admin.telegram') }}</p></div>
    </div>

    <div class="admin-card glass-luxury mb-8">
        <h2 class="admin-card__title">{{ __('admin.invitations') }}</h2>
        <table class="admin-table">
            <thead><tr><th>{{ __('admin.table.event') }}</th><th>{{ __('admin.status') }}</th><th>{{ __('admin.table.rsvp') }}</th><th></th></tr></thead>
            <tbody>
                @foreach ($invitations as $invitation)
                    <tr>
                        <td>
                            <p class="font-medium text-ink">{{ $invitation->displayTitle() }}</p>
                            <p class="text-xs text-ink-muted">{{ $invitation->event_type }}</p>
                        </td>
                        <td><span class="admin-badge admin-badge--{{ $invitation->status }}">{{ $invitation->statusLabel() }}</span></td>
                        <td>{{ $invitation->rsvp_responses_count }}</td>
                        <td><a href="{{ route('admin.invitations.show', $invitation) }}" class="text-sm font-semibold text-luxury-gold-dark">{{ __('admin.view') }}</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($invitations->hasPages())<div class="mt-4">{{ $invitations->links() }}</div>@endif
    </div>

    <div class="admin-card glass-luxury">
        <h2 class="admin-card__title">{{ __('admin.payments') }}</h2>
        <table class="admin-table">
            <thead><tr><th>{{ __('admin.table.provider') }}</th><th>{{ __('admin.table.amount') }}</th><th>{{ __('admin.status') }}</th><th>{{ __('admin.table.paid_at') }}</th></tr></thead>
            <tbody>
                @forelse ($payments as $invoice)
                    <tr>
                        <td>{{ strtoupper($invoice->provider) }}</td>
                        <td>{{ number_format($invoice->amount, 0, '.', ' ') }} {{ __('landing.currency') }}</td>
                        <td><span class="admin-badge admin-badge--{{ $invoice->status }}">{{ $invoice->status }}</span></td>
                        <td>{{ $invoice->paid_at?->timezone('Asia/Tashkent')->format('d.m.Y H:i') ?: '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-ink-muted">{{ __('admin.no_results') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
