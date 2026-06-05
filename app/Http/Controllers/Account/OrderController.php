<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = auth()->user()
            ->invitations()
            ->withCount('rsvpResponses')
            ->latest()
            ->paginate(10);

        return view('account.orders', [
            'title' => 'Zakazlarim — Taklifnoma',
            'orders' => $orders,
        ]);
    }
}
