<?php

namespace App\Services\Payments;

use App\Models\PaymentInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClickMerchantService
{
    public function __construct(
        private readonly PaymentActivationService $activationService,
    ) {}

    public function handle(Request $request): array
    {
        $action = (int) $request->input('action', -1);
        $clickTransId = (string) $request->input('click_trans_id', '');
        $serviceId = (string) $request->input('service_id', '');
        $merchantTransId = (string) $request->input('merchant_trans_id', '');
        $amount = (float) $request->input('amount', 0);
        $signTime = (string) $request->input('sign_time', '');
        $signString = (string) $request->input('sign_string', '');
        $merchantPrepareId = (string) $request->input('merchant_prepare_id', '');

        if (! $this->verifySignature(
            $clickTransId,
            $serviceId,
            $merchantTransId,
            $merchantPrepareId,
            $amount,
            $action,
            $signTime,
            $signString
        )) {
            return $this->error(-1, 'SIGN CHECK FAILED!');
        }

        $invoice = PaymentInvoice::query()
            ->where('merchant_trans_id', $merchantTransId)
            ->where('provider', PaymentInvoice::PROVIDER_CLICK)
            ->first();

        if (! $invoice) {
            return $this->error(-5, 'User does not exist');
        }

        if (! $this->amountMatches($invoice, $amount)) {
            return $this->error(-2, 'Incorrect parameter amount');
        }

        return match ($action) {
            0 => $this->prepare($invoice, $clickTransId),
            1 => $this->complete($invoice, $clickTransId, $merchantPrepareId),
            default => $this->error(-3, 'Action not found'),
        };
    }

    private function prepare(PaymentInvoice $invoice, string $clickTransId): array
    {
        if ($invoice->isPaid()) {
            return $this->error(-4, 'Already paid');
        }

        $prepareId = (int) $invoice->id;
        $invoice->markPrepared([
            'click' => [
                'click_trans_id' => $clickTransId,
                'merchant_prepare_id' => $prepareId,
                'prepared_at' => now()->toIso8601String(),
            ],
        ]);

        return [
            'click_trans_id' => $clickTransId,
            'merchant_trans_id' => $invoice->merchant_trans_id,
            'merchant_prepare_id' => $prepareId,
            'error' => 0,
            'error_note' => 'Success',
        ];
    }

    private function complete(PaymentInvoice $invoice, string $clickTransId, string $merchantPrepareId): array
    {
        $clickState = $invoice->provider_state['click'] ?? [];

        if ((string) ($clickState['merchant_prepare_id'] ?? '') !== $merchantPrepareId) {
            return $this->error(-6, 'Transaction does not exist');
        }

        if ($invoice->isPaid()) {
            return [
                'click_trans_id' => $clickTransId,
                'merchant_trans_id' => $invoice->merchant_trans_id,
                'merchant_confirm_id' => (int) $invoice->id,
                'error' => 0,
                'error_note' => 'Success',
            ];
        }

        $invoice->markPaid($clickTransId, [
            'click' => array_merge($clickState, [
                'click_trans_id' => $clickTransId,
                'completed_at' => now()->toIso8601String(),
            ]),
        ]);

        try {
            $this->activationService->activateFromInvoice($invoice);
        } catch (\Throwable $exception) {
            Log::error('Click activation failed', [
                'invoice' => $invoice->uuid,
                'error' => $exception->getMessage(),
            ]);

            return $this->error(-9, 'Transaction cancel');
        }

        return [
            'click_trans_id' => $clickTransId,
            'merchant_trans_id' => $invoice->merchant_trans_id,
            'merchant_confirm_id' => (int) $invoice->id,
            'error' => 0,
            'error_note' => 'Success',
        ];
    }

    public function buildCheckoutUrl(PaymentInvoice $invoice): string
    {
        $serviceId = (string) config('payments.click.service_id');
        $merchantId = (string) config('payments.click.merchant_id');
        $secretKey = (string) config('payments.click.secret_key');
        $amount = (int) $invoice->amount;

        $signString = md5($serviceId.$secretKey.$invoice->merchant_trans_id.$amount);

        $query = http_build_query([
            'service_id' => $serviceId,
            'merchant_id' => $merchantId,
            'amount' => $amount,
            'transaction_param' => $invoice->uuid,
            'merchant_trans_id' => $invoice->merchant_trans_id,
            'return_url' => route(config('payments.return_route'), [
                'provider' => PaymentInvoice::PROVIDER_CLICK,
                'invoice' => $invoice->uuid,
            ]),
            'sign_time' => time(),
            'sign_string' => $signString,
        ]);

        return config('payments.click.checkout_url').'?'.$query;
    }

    private function verifySignature(
        string $clickTransId,
        string $serviceId,
        string $merchantTransId,
        string $merchantPrepareId,
        float $amount,
        int $action,
        string $signTime,
        string $signString,
    ): bool {
        $secretKey = (string) config('payments.click.secret_key');

        if ($secretKey === '' || $signString === '') {
            return false;
        }

        if ((string) config('payments.click.service_id') !== $serviceId) {
            return false;
        }

        $digest = $action === 1
            ? md5($clickTransId.$serviceId.$secretKey.$merchantTransId.$merchantPrepareId.$amount.$action.$signTime)
            : md5($clickTransId.$serviceId.$secretKey.$merchantTransId.$amount.$action.$signTime);

        return hash_equals(strtolower($digest), strtolower($signString));
    }

    private function amountMatches(PaymentInvoice $invoice, float $amount): bool
    {
        return (int) round($amount) === (int) $invoice->amount;
    }

    private function error(int $code, string $note): array
    {
        return [
            'error' => $code,
            'error_note' => $note,
        ];
    }
}
