@extends('layouts.admin')

@section('admin')
    <div class="mb-8">
        <p class="section-label mb-3">{{ __('admin.payments') }}</p>
        <h1 class="font-serif text-display font-semibold text-ink">{{ __('admin.payments_title') }}</h1>
    </div>

    <div class="admin-stat-grid mb-8">
        <div class="admin-stat glass-luxury"><p class="admin-stat__value">{{ $summary['total'] }}</p><p class="admin-stat__label">Jami invoice</p></div>
        <div class="admin-stat glass-luxury"><p class="admin-stat__value">{{ $summary['paid'] }}</p><p class="admin-stat__label">{{ __('admin.stats.payments_paid') }}</p></div>
        <div class="admin-stat glass-luxury"><p class="admin-stat__value">{{ $summary['pending'] }}</p><p class="admin-stat__label">{{ __('admin.stats.payments_pending') }}</p></div>
        <div class="admin-stat glass-luxury"><p class="admin-stat__value text-lg">{{ $summary['revenue'] }}</p><p class="admin-stat__label">{{ __('admin.stats.revenue_total') }}</p></div>
    </div>

    <div class="admin-card glass-luxury">
        <form method="GET" class="admin-filters">
            <select name="status" class="admin-select">
                <option value="">{{ __('admin.filter_all') }}</option>
                <option value="paid" @selected($status === 'paid')>{{ __('admin.filter_paid') }}</option>
                <option value="pending" @selected($status === 'pending')>{{ __('admin.filter_pending') }}</option>
                <option value="prepared" @selected($status === 'prepared')>Prepared</option>
                <option value="failed" @selected($status === 'failed')>Failed</option>
            </select>
            <select name="provider" class="admin-select">
                <option value="">{{ __('admin.filter_all') }} provayder</option>
                <option value="click" @selected($provider === 'click')>Click</option>
                <option value="payme" @selected($provider === 'payme')>Payme</option>
            </select>
            <button type="submit" class="btn-outline-luxury text-sm">{{ __('admin.search') }}</button>
        </form>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>{{ __('admin.table.provider') }}</th>
                    <th>{{ __('admin.table.amount') }}</th>
                    <th>{{ __('admin.owner') }}</th>
                    <th>{{ __('admin.table.event') }}</th>
                    <th>{{ __('admin.status') }}</th>
                    <th>{{ __('admin.table.paid_at') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoices as $invoice)
                    <tr>
                        <td>{{ strtoupper($invoice->provider) }}</td>
                        <td class="font-semibold">{{ number_format($invoice->amount, 0, '.', ' ') }} {{ __('landing.currency') }}</td>
                        <td>{{ $invoice->user?->name ?? '—' }}</td>
                        <td>{{ $invoice->invitation?->displayTitle() ?? '—' }}</td>
                        <td><span class="admin-badge admin-badge--{{ $invoice->status }}">{{ $invoice->status }}</span></td>
                        <td>{{ $invoice->paid_at?->timezone('Asia/Tashkent')->format('d.m.Y H:i') ?: '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-ink-muted py-8 text-center">{{ __('admin.no_results') }}</td></tr>
                @endforelse
            </tbody>
        </table>

        @if ($invoices->hasPages())
            <div class="mt-6 pt-4 border-t border-white/30">{{ $invoices->links() }}</div>
        @endif
    </div>
@endsection
