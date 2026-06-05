<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()
            ->where('role', '!=', User::ROLE_ADMIN)
            ->withCount(['invitations', 'paymentInvoices', 'favorites']);

        if ($search = trim((string) $request->query('q'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return view('admin.users.index', [
            'title' => __('admin.users_title'),
            'users' => $query->latest()->paginate(15)->withQueryString(),
            'search' => $search ?? '',
        ]);
    }

    public function show(User $user): View
    {
        abort_if($user->isAdmin(), 404);

        $user->loadCount(['invitations', 'paymentInvoices', 'favorites']);

        $invitations = $user->invitations()
            ->withCount('rsvpResponses')
            ->latest()
            ->paginate(10);

        $payments = $user->paymentInvoices()
            ->with('invitation:id,slug,event_type,groom_name,bride_name,profile_meta')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.users.show', [
            'title' => $user->name.' — '.__('admin.users_title'),
            'user' => $user,
            'invitations' => $invitations,
            'payments' => $payments,
        ]);
    }
}
