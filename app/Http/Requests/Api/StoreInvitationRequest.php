<?php

namespace App\Http\Requests\Api;

use App\Support\BuilderEventProfile;
use App\Support\TemplateCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreInvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $eventData = $this->input('event_data', []);

        if (is_array($eventData)) {
            $profile = $eventData['profile'] ?? [];
            $slug = $this->input('template_slug', 'nikoh');
            $normalized = BuilderEventProfile::normalizeForStorage($slug, ['profile' => $profile]);

            $this->merge([
                'profile' => $profile,
                'profile_meta' => $normalized['profile_meta'],
                'groom_name' => $normalized['groom_name'],
                'bride_name' => $normalized['bride_name'],
            ]);
        }
    }

    public function rules(): array
    {
        $slug = (string) $this->input('template_slug', 'nikoh');

        return [
            'template_slug' => ['required', 'string', Rule::in(TemplateCatalog::slugs())],
            'event_type' => ['required', 'string', 'max:100'],
            'status' => ['sometimes', 'string', Rule::in(['draft', 'active'])],
            'custom_slug' => ['nullable', 'string', 'max:120', 'alpha_dash', 'unique:invitations,custom_slug'],
            'event_data' => ['required', 'array'],
            'event_data.profile' => ['required', 'array'],
            'event_data.schedule' => ['required', 'array'],
            'event_data.schedule.event_at' => ['required', 'date'],
            'event_data.schedule.event_city' => ['nullable', 'string', 'max:100'],
            'event_data.venue' => ['required', 'array'],
            'event_data.venue.name' => ['required', 'string', 'max:150'],
            'event_data.venue.address' => ['required', 'string', 'max:255'],
            'event_data.venue.map_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'event_data.venue.map_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'event_data.copy' => ['required', 'array'],
            'event_data.copy.invitation_text_1' => ['required', 'string', 'max:1000'],
            'event_data.copy.invitation_text_2' => ['nullable', 'string', 'max:1000'],
            'event_data.copy.family_signature' => ['nullable', 'string', 'max:150'],
            'event_data.media' => ['nullable', 'array'],
            'event_data.media.music_url' => ['nullable', 'string', 'max:500'],
            'event_data.dress_colors' => ['nullable', 'array', 'max:8'],
            'event_data.rsvp_enabled' => ['sometimes', 'boolean'],
            'profile' => ['nullable', 'array'],
            ...collect(BuilderEventProfile::fieldsForSlug($slug))
                ->mapWithKeys(fn (array $field) => [
                    "profile.{$field['key']}" => [
                        $field['required'] ? 'required' : 'nullable',
                        'string',
                        'max:80',
                    ],
                ])
                ->all(),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $slug = (string) $this->input('template_slug', 'nikoh');

            foreach (BuilderEventProfile::fieldsForSlug($slug) as $field) {
                if (! $field['required']) {
                    continue;
                }

                $value = trim((string) $this->input("profile.{$field['key']}", ''));

                if ($value === '') {
                    $validator->errors()->add("event_data.profile.{$field['key']}", __($field['label']).' majburiy.');
                }
            }
        });
    }
}
