<?php

namespace App\Http\Resources;

use App\Support\InvitationEventData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvitationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'template_slug' => $this->template_slug,
            'event_type' => $this->event_type,
            'status' => $this->status,
            'custom_slug' => $this->custom_slug,
            'public_url' => $this->publicUrl(),
            'display_title' => $this->displayTitle(),
            'event_data' => $this->event_data ?: InvitationEventData::fromInvitation($this->resource),
            'published_at' => $this->published_at?->toIso8601String(),
            'expires_at' => $this->expires_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
