@extends('layouts.admin')

@section('admin')
    <div class="mb-8">
        <p class="section-label mb-3">{{ __('admin.users') }}</p>
        <h1 class="font-serif text-display font-semibold text-ink">{{ __('admin.users_title') }}</h1>
    </div>

    <div class="admin-card glass-luxury">
        <form method="GET" class="admin-filters">
            <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('admin.search_placeholder') }}" class="admin-input min-w-[220px] flex-1">
            <button type="submit" class="btn-outline-luxury text-sm">{{ __('admin.search') }}</button>
        </form>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>{{ __('admin.table.name') }}</th>
                    <th>{{ __('admin.table.email') }}</th>
                    <th>{{ __('admin.table.invitations') }}</th>
                    <th>{{ __('admin.table.payments') }}</th>
                    <th>{{ __('admin.created_at') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td class="font-medium text-ink">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->invitations_count }}</td>
                        <td>{{ $user->payment_invoices_count }}</td>
                        <td>{{ $user->created_at?->timezone('Asia/Tashkent')->format('d.m.Y H:i') }}</td>
                        <td><a href="{{ route('admin.users.show', $user) }}" class="text-sm font-semibold text-luxury-gold-dark hover:underline">{{ __('admin.view') }}</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-ink-muted py-8 text-center">{{ __('admin.no_results') }}</td></tr>
                @endforelse
            </tbody>
        </table>

        @if ($users->hasPages())
            <div class="mt-6 pt-4 border-t border-white/30">{{ $users->links() }}</div>
        @endif
    </div>
@endsection
