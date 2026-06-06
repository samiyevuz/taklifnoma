<?php

namespace App\Services\Payments;

use App\Models\Invitation;
use App\Models\PaymentInvoice;
use App\Support\PlanEntitlements;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PaymentActivationService
{
    public function activateFromInvoice(PaymentInvoice $invoice): Invitation
    {
        return DB::transaction(function () use ($invoice) {
            $invoice->refresh();
            $invitation = $invoice->invitation()->lockForUpdate()->firstOrFail();

            if ($invoice->isPaid() && $invitation->status === Invitation::STATUS_ACTIVE) {
                return $invitation;
            }

            if (! $invoice->isPaid()) {
                $invoice->markPaid($invoice->provider_trans_id);
            }

            $plan = PlanEntitlements::forTheme($invoice->plan_tier);

            $invitation->update([
                'status' => Invitation::STATUS_ACTIVE,
                'published_at' => $invitation->published_at ?? now(),
                'plan_tier' => $invoice->plan_tier ?? $invitation->plan_tier,
                'guest_limit' => $plan['guest_limit'],
            ]);

            $this->clearPublicCache($invitation);

            return $invitation->fresh();
        });
    }

    private function clearPublicCache(Invitation $invitation): void
    {
        foreach (array_filter([$invitation->custom_slug, $invitation->slug, $invitation->uuid]) as $key) {
            Cache::forget('invitation.public.'.$key);
        }
    }
}
