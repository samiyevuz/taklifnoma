@props([
    'invitation',
    'snapshot',
    'pollUrl',
    'compact' => false,
])

@php
    $recent = collect($snapshot['recent'] ?? []);
@endphp

<div
    class="rsvp-panel rsvp-live-panel {{ $compact ? 'rsvp-live-panel--compact' : '' }}"
    data-rsvp-live-panel
    data-poll-url="{{ $pollUrl }}"
    data-poll-interval="8000"
    data-latest-id="{{ $recent->first()['id'] ?? 0 }}"
    aria-live="polite"
>
    <div class="rsvp-live-panel__header">
        <div>
            <p class="text-xs font-semibold tracking-widest text-luxury-gold-dark uppercase">{{ __('builder.rsvp_live_label') }}</p>
            <h3 class="mt-1 font-serif text-xl font-semibold text-ink">{{ __('builder.rsvp_live_title') }}</h3>
            @unless ($compact)
                <p class="mt-1 text-sm text-ink-soft">{{ $snapshot['event_title'] ?? $invitation->displayTitle() }}</p>
            @endunless
        </div>
        <span class="rsvp-live-panel__badge" data-rsvp-live-badge>
            <span class="rsvp-live-panel__pulse" aria-hidden="true"></span>
            <span class="rsvp-live-panel__pulse-dot" aria-hidden="true"></span>
            {{ __('builder.rsvp_live_badge') }}
        </span>
    </div>

    <div class="mb-6 grid grid-cols-3 gap-3 text-center">
        <div class="rsvp-stat-box rounded-xl p-3">
            <p class="font-serif text-2xl font-bold" data-rsvp-count="attending">{{ $snapshot['attending'] ?? 0 }}</p>
            <p class="mt-0.5 text-xs">{{ __('builder.stats_attending') }}</p>
        </div>
        <div class="rsvp-stat-box rounded-xl p-3">
            <p class="font-serif text-2xl font-bold" data-rsvp-count="declined">{{ $snapshot['declined'] ?? 0 }}</p>
            <p class="mt-0.5 text-xs">{{ __('builder.stats_declined') }}</p>
        </div>
        <div class="rsvp-stat-box rounded-xl p-3">
            <p class="font-serif text-2xl font-bold" data-rsvp-count="guests">{{ $snapshot['total_guests'] ?? 0 }}</p>
            <p class="mt-0.5 text-xs">{{ __('builder.stats_guests') }}</p>
        </div>
    </div>

    <p class="mb-2 text-sm font-medium text-ink-soft">{{ __('builder.rsvp_live_progress') }}</p>
    <div class="rsvp-bar">
        <div class="rsvp-bar__fill" data-rsvp-bar style="width: {{ $snapshot['confirmation_rate'] ?? 0 }}%"></div>
    </div>
    <p class="mt-2 text-right text-xs text-ink-muted">
        <span data-rsvp-percent>{{ $snapshot['confirmation_rate'] ?? 0 }}</span>% {{ __('builder.rsvp_live_confirmed') }}
    </p>

    <div class="rsvp-live-panel__feed-header">
        <p class="text-sm font-medium text-ink-soft">{{ __('builder.rsvp_live_recent') }}</p>
        <p class="text-xs text-ink-muted" data-rsvp-updated>
            @if (! empty($snapshot['fetched_at']))
                {{ __('builder.rsvp_live_updated') }}
            @endif
        </p>
    </div>

    <ul class="rsvp-live-panel__feed" data-rsvp-feed>
        @forelse ($recent as $item)
            <li
                class="rsvp-stat"
                data-rsvp-item
                data-rsvp-id="{{ $item['id'] }}"
            >
                <span class="text-sm font-medium text-ink">{{ $item['guest_name'] }}</span>
                <span class="text-xs font-semibold {{ $item['is_attending'] ? 'text-luxury-emerald' : 'text-ink-muted' }}">
                    {{ $item['guest_summary'] }}
                </span>
            </li>
        @empty
            <li class="rsvp-live-panel__empty" data-rsvp-empty>
                {{ __('builder.rsvp_live_empty') }}
            </li>
        @endforelse
    </ul>
</div>
