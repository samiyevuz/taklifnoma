<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rule;

class UpdateInvitationRequest extends StoreInvitationRequest
{
    public function rules(): array
    {
        $invitation = $this->route('invitation');

        return array_merge(parent::rules(), [
            'custom_slug' => [
                'nullable',
                'string',
                'max:120',
                'alpha_dash',
                Rule::unique('invitations', 'custom_slug')->ignore($invitation?->id),
            ],
            'status' => ['sometimes', 'string', Rule::in(['draft', 'active', 'expired'])],
        ]);
    }
}
