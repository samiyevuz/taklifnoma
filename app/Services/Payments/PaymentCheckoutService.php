<?php

namespace App\Services\Payments;

use App\Models\Invitation;
use App\Models\PaymentInvoice;
use App\Models\User;
use App\Services\InvitationService;
use App\Support\TemplateCatalog;
use Illuminate\Support\Facades\DB;

class PaymentCheckoutService
{
    public function __construct(
        private readonly InvitationService $invitationService,
        private readonly ClickMerchantService $clickMerchantService,
    ) {}

    public function generateInvoice(User $user, array $payload): array
    {
        return DB::transaction(function () use ($user, $payload) {
            $provider = $payload['payment_provider'];
            $templateSlug = $payload['template_slug'] ?? 'nikoh';
            $template = TemplateCatalog::find($templateSlug) ?? TemplateCatalog::find('nikoh');
            $amount = (int) ($template['price_amount'] ?? 89000);

            $invitation = $this->resolveInvitation($user, $payload);
            $amountTiyin = $amount * 100;

            $invoice = PaymentInvoice::query()->create([
                'user_id' => $user->id,
                'invitation_id' => $invitation->id,
                'provider' => $provider,
                'amount' => $amount,
                'amount_tiyin' => $amountTiyin,
                'currency' => config('payments.currency', 'UZS'),
                'template_slug' => $templateSlug,
                'status' => PaymentInvoice::STATUS_PENDING,
            ]);

            return [
                'invoice' => $invoice->fresh(['invitation']),
                'checkout' => $this->buildCheckoutPayload($invoice),
            ];
        });
    }

    private function resolveInvitation(User $user, array $payload): Invitation
    {
        if (! empty($payload['invitation_id'])) {
            $invitation = Invitation::query()
                ->where('id', $payload['invitation_id'])
                ->where('user_id', $user->id)
                ->firstOrFail();

            $this->invitationService->update($invitation, $payload, false);

            return $invitation->fresh();
        }

        $payload['user_id'] = $user->id;

        return $this->invitationService->create($payload, false);
    }

    private function buildCheckoutPayload(PaymentInvoice $invoice): array
    {
        $invitation = $invoice->invitation;

        return match ($invoice->provider) {
            PaymentInvoice::PROVIDER_CLICK => [
                'provider' => PaymentInvoice::PROVIDER_CLICK,
                'redirect_url' => $this->clickMerchantService->buildCheckoutUrl($invoice),
            ],
            PaymentInvoice::PROVIDER_PAYME => [
                'provider' => PaymentInvoice::PROVIDER_PAYME,
                'redirect_url' => $this->buildPaymeCheckoutUrl($invoice),
            ],
            default => throw new \InvalidArgumentException('Unsupported payment provider'),
        } + [
            'invoice_id' => $invoice->uuid,
            'merchant_trans_id' => $invoice->merchant_trans_id,
            'amount' => $invoice->amount,
            'amount_label' => number_format($invoice->amount, 0, '.', ' ').' '.config('payments.currency', 'UZS'),
            'public_url' => $invitation->publicUrl(),
            'custom_slug' => $invitation->custom_slug,
            'template_slug' => $invoice->template_slug,
        ];
    }

    private function buildPaymeCheckoutUrl(PaymentInvoice $invoice): string
    {
        $merchantId = (string) config('payments.payme.merchant_id');
        $checkoutBase = rtrim((string) config('payments.payme.checkout_url'), '/');

        $params = [
            'm' => $merchantId,
            'ac' => ['invoice' => $invoice->uuid],
            'a' => $invoice->amount_tiyin,
            'c' => route(config('payments.return_route'), [
                'provider' => PaymentInvoice::PROVIDER_PAYME,
                'invoice' => $invoice->uuid,
            ]),
            'l' => app()->getLocale() === 'ru' ? 'ru' : (app()->getLocale() === 'en' ? 'en' : 'uz'),
        ];

        return $checkoutBase.'/'.base64_encode(json_encode($params, JSON_UNESCAPED_UNICODE));
    }
}
