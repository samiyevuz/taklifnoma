<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RsvpResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RsvpController extends Controller
{
    public function index(Request $request): View
    {
        $query = RsvpResponse::query()
            ->with(['invitation:id,slug,event_type,groom_name,bride_name,profile_meta,user_id', 'invitation.user:id,name,email']);

        if ($request->query('attending') === '1') {
            $query->where('is_attending', true);
        } elseif ($request->query('attending') === '0') {
            $query->where('is_attending', false);
        }

        if ($search = trim((string) $request->query('q'))) {
            $query->where('guest_name', 'like', "%{$search}%");
        }

        $stats = [
            'total' => RsvpResponse::query()->count(),
            'attending' => RsvpResponse::query()->where('is_attending', true)->count(),
            'declined' => RsvpResponse::query()->where('is_attending', false)->count(),
            'guests' => (int) RsvpResponse::query()
                ->where('is_attending', true)
                ->selectRaw('SUM(adults_count + children_count) as total')
                ->value('total'),
        ];

        return view('admin.rsvps.index', [
            'title' => __('admin.rsvps_title'),
            'responses' => $query->latest()->paginate(20)->withQueryString(),
            'stats' => $stats,
            'search' => $search ?? '',
            'attending' => $request->query('attending', ''),
        ]);
    }
}
