<?php

namespace App\Services\Payments;

use App\Models\PaymentInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymeMerchantService
{
    public function __construct(
        private readonly PaymentActivationService $activationService,
    ) {}

    public function handle(Request $request): array
    {
        if (! $this->authorize($request)) {
            return $this->errorResponse($request->input('id'), -32504, 'Avtorizatsiya xatosi');
        }

        $payload = $request->all();
        $id = $payload['id'] ?? null;
        $method = $payload['method'] ?? null;
        $params = $payload['params'] ?? [];

        return match ($method) {
            'CheckPerformTransaction' => $this->checkPerformTransaction($id, $params),
            'CreateTransaction' => $this->createTransaction($id, $params),
            'PerformTransaction' => $this->performTransaction($id, $params),
            'CancelTransaction' => $this->cancelTransaction($id, $params),
            'CheckTransaction' => $this->checkTransaction($id, $params),
            default => $this->errorResponse($id, -32601, 'Metod topilmadi'),
        };
    }

    private function authorize(Request $request): bool
    {
        $merchantId = (string) config('payments.payme.merchant_id');
        $secretKey = (string) config('payments.payme.secret_key');

        if ($merchantId === '' || $secretKey === '') {
            return false;
        }

        $authorization = (string) $request->header('Authorization', '');

        if (! str_starts_with($authorization, 'Basic ')) {
            return false;
        }

        $decoded = base64_decode(substr($authorization, 6), true);

        if ($decoded === false) {
            return false;
        }

        [$login, $password] = array_pad(explode(':', $decoded, 2), 2, '');

        return hash_equals($merchantId, $login) && hash_equals($secretKey, $password);
    }

    private function checkPerformTransaction(mixed $id, array $params): array
    {
        $invoice = $this->resolveInvoice($params);

        if (! $invoice) {
            return $this->errorResponse($id, -31050, 'Buyurtma topilmadi');
        }

        if (! $this->amountMatches($invoice, (int) ($params['amount'] ?? 0))) {
            return $this->errorResponse($id, -31001, 'Noto\'g\'ri summa');
        }

        if ($invoice->isPaid()) {
            return $this->errorResponse($id, -31008, 'Tranzaksiya bajarilgan');
        }

        return $this->successResponse($id, ['allow' => true]);
    }

    private function createTransaction(mixed $id, array $params): array
    {
        $invoice = $this->resolveInvoice($params);

        if (! $invoice) {
            return $this->errorResponse($id, -31050, 'Buyurtma topilmadi');
        }

        if (! $this->amountMatches($invoice, (int) ($params['amount'] ?? 0))) {
            return $this->errorResponse($id, -31001, 'Noto\'g\'ri summa');
        }

        $existing = $this->findPaymeTransaction($invoice, (string) ($params['id'] ?? ''));

        if ($existing) {
            return $this->successResponse($id, $existing);
        }

        if ($invoice->isPaid()) {
            return $this->errorResponse($id, -31008, 'Tranzaksiya bajarilgan');
        }

        $time = (int) ($params['time'] ?? (now()->timestamp * 1000));
        $transactionId = (string) $params['id'];

        $state = array_merge($invoice->provider_state ?? [], [
            'payme' => [
                'id' => $transactionId,
                'time' => $time,
                'state' => 1,
                'create_time' => $time,
            ],
        ]);

        $invoice->markPrepared(['payme' => $state['payme']]);
        $invoice->update(['provider_state' => $state, 'provider_trans_id' => $transactionId]);

        return $this->successResponse($id, [
            'create_time' => $time,
            'transaction' => $transactionId,
            'state' => 1,
        ]);
    }

    private function performTransaction(mixed $id, array $params): array
    {
        $invoice = $this->resolveInvoiceByTransaction((string) ($params['id'] ?? ''));

        if (! $invoice) {
            return $this->errorResponse($id, -31003, 'Tranzaksiya topilmadi');
        }

        $payme = $invoice->provider_state['payme'] ?? [];

        if (($payme['state'] ?? null) === 2) {
            return $this->successResponse($id, [
                'transaction' => $payme['id'],
                'perform_time' => $payme['perform_time'] ?? (now()->timestamp * 1000),
                'state' => 2,
            ]);
        }

        $performTime = now()->timestamp * 1000;
        $transactionId = (string) $params['id'];

        $invoice->markPaid($transactionId, [
            'payme' => array_merge($payme, [
                'id' => $transactionId,
                'state' => 2,
                'perform_time' => $performTime,
            ]),
        ]);

        try {
            $this->activationService->activateFromInvoice($invoice);
        } catch (\Throwable $exception) {
            Log::error('Payme activation failed', [
                'invoice' => $invoice->uuid,
                'error' => $exception->getMessage(),
            ]);

            return $this->errorResponse($id, -32400, 'Faollashtirish xatosi');
        }

        return $this->successResponse($id, [
            'transaction' => $transactionId,
            'perform_time' => $performTime,
            'state' => 2,
        ]);
    }

    private function cancelTransaction(mixed $id, array $params): array
    {
        $invoice = $this->resolveInvoiceByTransaction((string) ($params['id'] ?? ''));

        if (! $invoice) {
            return $this->errorResponse($id, -31003, 'Tranzaksiya topilmadi');
        }

        $payme = $invoice->provider_state['payme'] ?? [];
        $cancelTime = now()->timestamp * 1000;
        $reason = (int) ($params['reason'] ?? 1);
        $state = ($payme['state'] ?? 1) === 2 ? -2 : -1;

        $invoice->markCancelled([
            'payme' => array_merge($payme, [
                'state' => $state,
                'cancel_time' => $cancelTime,
                'reason' => $reason,
            ]),
        ]);

        return $this->successResponse($id, [
            'transaction' => (string) $params['id'],
            'cancel_time' => $cancelTime,
            'state' => $state,
        ]);
    }

    private function checkTransaction(mixed $id, array $params): array
    {
        $invoice = $this->resolveInvoiceByTransaction((string) ($params['id'] ?? ''));

        if (! $invoice) {
            return $this->errorResponse($id, -31003, 'Tranzaksiya topilmadi');
        }

        $payme = $invoice->provider_state['payme'] ?? [];

        return $this->successResponse($id, [
            'create_time' => $payme['create_time'] ?? (now()->timestamp * 1000),
            'perform_time' => $payme['perform_time'] ?? 0,
            'cancel_time' => $payme['cancel_time'] ?? 0,
            'transaction' => (string) $params['id'],
            'state' => $payme['state'] ?? 1,
            'reason' => $payme['reason'] ?? null,
        ]);
    }

    private function resolveInvoice(array $params): ?PaymentInvoice
    {
        $account = $params['account'] ?? [];
        $invoiceUuid = $account['invoice'] ?? $account['order_id'] ?? null;

        if (! $invoiceUuid) {
            return null;
        }

        return PaymentInvoice::query()
            ->where('uuid', $invoiceUuid)
            ->where('provider', PaymentInvoice::PROVIDER_PAYME)
            ->first();
    }

    private function resolveInvoiceByTransaction(string $transactionId): ?PaymentInvoice
    {
        if ($transactionId === '') {
            return null;
        }

        return PaymentInvoice::query()
            ->where('provider', PaymentInvoice::PROVIDER_PAYME)
            ->where('provider_trans_id', $transactionId)
            ->first();
    }

    private function findPaymeTransaction(PaymentInvoice $invoice, string $transactionId): ?array
    {
        $payme = $invoice->provider_state['payme'] ?? [];

        if (($payme['id'] ?? null) !== $transactionId) {
            return null;
        }

        return [
            'create_time' => $payme['create_time'] ?? (now()->timestamp * 1000),
            'transaction' => $transactionId,
            'state' => $payme['state'] ?? 1,
        ];
    }

    private function amountMatches(PaymentInvoice $invoice, int $amountTiyin): bool
    {
        return $amountTiyin > 0 && $amountTiyin === (int) $invoice->amount_tiyin;
    }

    private function successResponse(mixed $id, array $result): array
    {
        return [
            'jsonrpc' => '2.0',
            'id' => $id,
            'result' => $result,
        ];
    }

    private function errorResponse(mixed $id, int $code, string $message): array
    {
        return [
            'jsonrpc' => '2.0',
            'id' => $id,
            'error' => [
                'code' => $code,
                'message' => [
                    'uz' => $message,
                    'ru' => $message,
                    'en' => $message,
                ],
            ],
        ];
    }
}
