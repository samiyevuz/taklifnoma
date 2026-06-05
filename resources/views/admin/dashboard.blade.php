@extends('layouts.admin')

@section('admin')
    <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="section-label mb-3">{{ __('admin.panel') }}</p>
            <h1 class="font-serif text-display font-semibold text-ink">{{ __('admin.dashboard_title') }}</h1>
            <p class="mt-2 text-ink-soft">{{ __('admin.dashboard_subtitle') }}</p>
        </div>
        <span class="admin-live-badge" data-admin-live-badge>
            <span class="admin-live-badge__dot" aria-hidden="true"></span>
            {{ __('admin.live') }}
        </span>
    </div>

    <div class="admin-stat-grid mb-8" id="admin-stats-grid" data-stats-url="{{ route('admin.stats') }}">
        @foreach ([
            ['key' => 'users_total', 'meta' => 'users_today'],
            ['key' => 'invitations_total', 'meta' => 'invitations_today'],
            ['key' => 'invitations_active', 'meta' => null],
            ['key' => 'rsvp_total', 'meta' => 'rsvp_today'],
            ['key' => 'rsvp_attending', 'meta' => 'rsvp_guests'],
            ['key' => 'revenue_total', 'meta' => 'revenue_today'],
            ['key' => 'payments_paid', 'meta' => 'payments_pending'],
            ['key' => 'telegram_linked', 'meta' => null],
        ] as $item)
            <div class="admin-stat glass-luxury">
                <p class="admin-stat__value" data-stat="{{ $item['key'] }}">
                    @if (str_contains($item['key'], 'revenue'))
                        {{ number_format($stats[$item['key']], 0, '.', ' ') }}
                    @else
                        {{ $stats[$item['key']] }}
                    @endif
                </p>
                <p class="admin-stat__label">{{ __('admin.stats.'.$item['key']) }}</p>
                @if ($item['meta'])
                    <p class="admin-stat__meta" data-stat-meta="{{ $item['meta'] }}">
                        @if (str_contains($item['meta'], 'revenue'))
                            +{{ number_format($stats[$item['meta']], 0, '.', ' ') }} {{ __('landing.currency') }}
                        @else
                            +{{ $stats[$item['meta']] }}
                        @endif
                    </p>
                @endif
            </div>
        @endforeach
    </div>

    <div class="admin-card glass-luxury mb-8">
        <h2 class="admin-card__title">{{ __('admin.sections.activity') }}</h2>
        @php $maxRsvp = max(1, max($stats['chart']['rsvps'])); @endphp
        <div class="admin-chart" aria-hidden="true">
            @foreach ($stats['chart']['labels'] as $i => $label)
                <div class="admin-chart__bar-wrap">
                    <div
                        class="admin-chart__bar is-rsvp"
                        style="height: {{ max(8, ($stats['chart']['rsvps'][$i] / $maxRsvp) * 100) }}%"
                        title="RSVP: {{ $stats['chart']['rsvps'][$i] }}"
                    ></div>
                    <span class="admin-chart__label">{{ $label }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="admin-grid-2 mb-8">
        <div class="admin-card glass-luxury">
            <h2 class="admin-card__title">{{ __('admin.sections.top_templates') }}</h2>
            <table class="admin-table">
                <thead><tr><th>Shablon</th><th>Soni</th></tr></thead>
                <tbody>
                    @forelse ($stats['top_templates'] as $template)
                        <tr>
                            <td class="font-medium text-ink">{{ $template['slug'] }}</td>
                            <td>{{ $template['total'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-ink-muted">{{ __('admin.no_results') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="admin-card glass-luxury">
            <h2 class="admin-card__title">{{ __('admin.sections.revenue_providers') }}</h2>
            <table class="admin-table">
                <thead><tr><th>{{ __('admin.table.provider') }}</th><th>{{ __('admin.table.amount') }}</th><th>Soni</th></tr></thead>
                <tbody>
                    @forelse ($stats['revenue_by_provider'] as $row)
                        <tr>
                            <td>{{ strtoupper($row['provider']) }}</td>
                            <td>{{ number_format($row['total'], 0, '.', ' ') }} {{ __('landing.currency') }}</td>
                            <td>{{ $row['count'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-ink-muted">{{ __('admin.no_results') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-grid-2">
        <div class="admin-card glass-luxury">
            <div class="flex items-center justify-between mb-3">
                <h2 class="admin-card__title mb-0">{{ __('admin.sections.recent_invitations') }}</h2>
                <a href="{{ route('admin.invitations.index') }}" class="text-sm font-semibold text-luxury-gold-dark hover:underline">{{ __('account.view_all') }} →</a>
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ __('admin.table.event') }}</th>
                        <th>{{ __('admin.owner') }}</th>
                        <th>{{ __('admin.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stats['recent_invitations'] as $row)
                        <tr>
                            <td>
                                <a href="{{ route('admin.invitations.show', $row['id']) }}" class="font-medium text-ink hover:text-luxury-gold-dark">{{ $row['title'] }}</a>
                                <p class="text-xs text-ink-muted">{{ $row['created_at'] }}</p>
                            </td>
                            <td>{{ $row['owner'] }}</td>
                            <td><span class="admin-badge admin-badge--{{ $row['status'] }}">{{ $row['status_label'] }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="admin-card glass-luxury">
            <div class="flex items-center justify-between mb-3">
                <h2 class="admin-card__title mb-0">{{ __('admin.sections.recent_rsvps') }}</h2>
                <a href="{{ route('admin.rsvps.index') }}" class="text-sm font-semibold text-luxury-gold-dark hover:underline">{{ __('account.view_all') }} →</a>
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ __('admin.table.guest') }}</th>
                        <th>{{ __('admin.table.event') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stats['recent_rsvps'] as $row)
                        <tr>
                            <td>
                                <p class="font-medium text-ink">{{ $row['guest_name'] }}</p>
                                <p class="text-xs text-ink-muted">{{ $row['guest_summary'] }}</p>
                            </td>
                            <td>
                                <p class="text-sm text-ink">{{ $row['invitation_title'] }}</p>
                                <p class="text-xs text-ink-muted">{{ $row['created_at'] }}</p>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
