<?php

namespace App\Http\Requests;

use App\Support\TemplateCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('dress_colors_json') && is_string($this->dress_colors_json)) {
            $decoded = json_decode($this->dress_colors_json, true);

            if (is_array($decoded)) {
                $this->merge(['dress_colors' => $decoded]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'groom_name' => ['required', 'string', 'max:80'],
            'bride_name' => ['required', 'string', 'max:80'],
            'event_type' => ['required', 'string', 'max:100'],
            'event_at' => ['required', 'date'],
            'event_city' => ['nullable', 'string', 'max:100'],
            'venue_name' => ['required', 'string', 'max:150'],
            'venue_address' => ['required', 'string', 'max:255'],
            'map_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'map_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'invitation_text_1' => ['required', 'string', 'max:1000'],
            'invitation_text_2' => ['nullable', 'string', 'max:1000'],
            'family_signature' => ['nullable', 'string', 'max:150'],
            'music_url' => ['nullable', 'string', 'max:500'],
            'dress_colors' => ['nullable', 'array', 'max:8'],
            'dress_colors.*.name' => ['required_with:dress_colors', 'string', 'max:40'],
            'dress_colors.*.hex' => ['required_with:dress_colors', 'string', 'max:7'],
            'dress_colors.*.note' => ['nullable', 'string', 'max:200'],
            'rsvp_enabled' => ['sometimes', 'boolean'],
            'slug' => [
                'nullable',
                'string',
                'max:80',
                'alpha_dash',
                Rule::unique('invitations', 'slug')->ignore($this->route('invitation')),
            ],
            'publish' => ['sometimes', 'boolean'],
            'template' => [
                'nullable',
                'string',
                'max:80',
                Rule::in(array_column(TemplateCatalog::definitions(), 'template')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'groom_name.required' => 'Kuyov ismi majburiy.',
            'bride_name.required' => 'Kelin ismi majburiy.',
            'event_at.required' => 'Tadbir sanasi majburiy.',
            'venue_name.required' => 'Joy nomi majburiy.',
            'venue_address.required' => 'Manzil majburiy.',
            'invitation_text_1.required' => 'Taklifnoma matni majburiy.',
        ];
    }
}
