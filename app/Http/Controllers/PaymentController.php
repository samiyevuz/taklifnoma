<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateInvoiceRequest;
use App\Models\PaymentInvoice;
use App\Services\InvitationMediaService;
use App\Services\Payments\ClickMerchantService;
use App\Services\Payments\PaymentCheckoutService;
use App\Services\Payments\PaymeMerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentCheckoutService $checkoutService,
        private readonly InvitationMediaService $mediaService,
        private readonly PaymeMerchantService $paymeMerchantService,
        private readonly ClickMerchantService $clickMerchantService,
    ) {}

    public function generateInvoice(GenerateInvoiceRequest $request): JsonResponse
    {
        $result = $this->checkoutService->generateInvoice(
            $request->user(),
            $request->validated()
        );

        $invoice = $result['invoice'];
        $checkout = $result['checkout'];

        if ($invoice->invitation) {
            $this->mediaService->sync($request, $invoice->invitation);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'invoice_id' => $invoice->uuid,
                'merchant_trans_id' => $invoice->merchant_trans_id,
                'provider' => $invoice->provider,
                'amount' => $invoice->amount,
                'amount_label' => $checkout['amount_label'],
                'redirect_url' => $checkout['redirect_url'],
                'public_url' => $checkout['public_url'],
                'custom_slug' => $checkout['custom_slug'],
                'invitation_id' => $invoice->invitation_id,
                'form_action' => route('builder.update', $invoice->invitation_id),
                'status' => $invoice->status,
            ],
        ]);
    }

    public function handlePaymeWebhook(Request $request): JsonResponse
    {
        $response = $this->paymeMerchantService->handle($request);

        return response()->json($response);
    }

    public function handleClickWebhook(Request $request): JsonResponse
    {
        $response = $this->clickMerchantService->handle($request);

        return response()->json($response);
    }

    public function return(Request $request): View
    {
        $invoice = null;

        if ($request->filled('invoice')) {
            $invoice = PaymentInvoice::query()
                ->where('uuid', $request->string('invoice'))
                ->with('invitation')
                ->first();
        }

        return view('payments.return', [
            'title' => __('builder.payment_return_title'),
            'invoice' => $invoice,
            'provider' => $request->string('provider'),
            'isPaid' => $invoice?->isPaid() ?? false,
            'publicUrl' => $invoice?->invitation?->publicUrl(),
        ]);
    }
}
