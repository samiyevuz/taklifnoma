<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Services\Rsvp\RsvpDashboardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function __construct(
        private readonly RsvpDashboardService $rsvpDashboard,
    ) {}

    public function index(Request $request): View
    {
        $query = Invitation::query()
            ->with('user:id,name,email')
            ->withCount('rsvpResponses');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($search = trim((string) $request->query('q'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('slug', 'like', "%{$search}%")
                    ->orWhere('event_type', 'like', "%{$search}%")
                    ->orWhere('groom_name', 'like', "%{$search}%")
                    ->orWhere('bride_name', 'like', "%{$search}%");
            });
        }

        return view('admin.invitations.index', [
            'title' => __('admin.invitations_title'),
            'invitations' => $query->latest()->paginate(15)->withQueryString(),
            'status' => $status ?? '',
            'search' => $search ?? '',
        ]);
    }

    public function show(Invitation $invitation): View
    {
        $invitation->load(['user:id,name,email,phone', 'paymentInvoices' => fn ($q) => $q->latest()->limit(5)]);

        return view('admin.invitations.show', [
            'title' => $invitation->displayTitle().' — '.__('admin.invitations_title'),
            'invitation' => $invitation,
            'rsvpSnapshot' => $this->rsvpDashboard->snapshot($invitation),
        ]);
    }

    public function updateStatus(Request $request, Invitation $invitation): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:draft,active,expired'],
        ]);

        $payload = ['status' => $validated['status']];

        if ($validated['status'] === Invitation::STATUS_ACTIVE && ! $invitation->published_at) {
            $payload['published_at'] = now();
        }

        if ($validated['status'] === Invitation::STATUS_EXPIRED) {
            $payload['expires_at'] = now();
        }

        $invitation->update($payload);

        return back()->with('success', __('admin.invitation_status_updated'));
    }

    public function destroy(Invitation $invitation): RedirectResponse
    {
        $invitation->delete();

        return redirect()
            ->route('admin.invitations.index')
            ->with('success', __('admin.invitation_deleted'));
    }
}
