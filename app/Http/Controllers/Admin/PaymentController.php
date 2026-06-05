<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentInvoice;
use App\Services\Admin\AdminStatisticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        private readonly AdminStatisticsService $statistics,
    ) {}

    public function index(Request $request): View
    {
        $query = PaymentInvoice::query()
            ->with(['user:id,name,email', 'invitation:id,slug,event_type,groom_name,bride_name,profile_meta']);

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($provider = $request->query('provider')) {
            $query->where('provider', $provider);
        }

        $summary = [
            'total' => PaymentInvoice::query()->count(),
            'paid' => PaymentInvoice::query()->where('status', PaymentInvoice::STATUS_PAID)->count(),
            'pending' => PaymentInvoice::query()->whereIn('status', [
                PaymentInvoice::STATUS_PENDING,
                PaymentInvoice::STATUS_PREPARED,
            ])->count(),
            'revenue' => $this->statistics->formatMoney((int) PaymentInvoice::query()
                ->where('status', PaymentInvoice::STATUS_PAID)
                ->sum('amount')),
        ];

        return view('admin.payments.index', [
            'title' => __('admin.payments_title'),
            'invoices' => $query->latest()->paginate(15)->withQueryString(),
            'summary' => $summary,
            'status' => $status ?? '',
            'provider' => $provider ?? '',
        ]);
    }
}
