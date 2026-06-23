<?php

namespace App\Http\Requests;

use App\Models\RsvpResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRsvpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'guest_name' => ['required', 'string', 'max:120'],
            'status' => ['required', Rule::in([
                RsvpResponse::STATUS_ATTENDING,
                RsvpResponse::STATUS_DECLINED,
            ])],
            'adults_count' => ['required_if:status,attending', 'integer', 'min:1', 'max:20'],
            'children_count' => ['nullable', 'integer', 'min:0', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'guest_name.required' => __('invitation.guest_name_required'),
            'status.required' => __('invitation.status_required'),
        ];
    }
}
