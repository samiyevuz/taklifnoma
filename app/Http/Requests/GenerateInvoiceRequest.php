<?php

namespace App\Http\Requests;

use App\Models\PaymentInvoice;
use App\Support\BuilderEventProfile;
use App\Support\TemplateCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class GenerateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('dress_colors_json') && is_string($this->dress_colors_json)) {
            $decoded = json_decode($this->dress_colors_json, true);

            if (is_array($decoded)) {
                $this->merge(['dress_colors' => $decoded]);
            }
        }

        $slug = (string) $this->input('template_slug', 'nikoh');
        $normalized = BuilderEventProfile::normalizeForStorage($slug, [
            'profile' => $this->input('profile', []),
        ]);

        $this->merge([
            'groom_name' => $normalized['groom_name'],
            'bride_name' => $normalized['bride_name'],
            'profile_meta' => $normalized['profile_meta'],
        ]);
    }

    public function rules(): array
    {
        $slug = (string) $this->input('template_slug', 'nikoh');

        return [
            'payment_provider' => ['required', 'string', Rule::in([
                PaymentInvoice::PROVIDER_CLICK,
                PaymentInvoice::PROVIDER_PAYME,
            ])],
            'template_slug' => ['required', 'string', Rule::in(TemplateCatalog::slugs())],
            'invitation_id' => ['nullable', 'integer', 'exists:invitations,id'],
            'profile' => ['nullable', 'array'],
            'profile_meta' => ['nullable', 'array'],
            'groom_name' => ['required', 'string', 'max:80'],
            'bride_name' => ['nullable', 'string', 'max:80'],
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
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'music_file' => ['nullable', 'file', 'mimes:mp3,m4a,aac,ogg,wav', 'max:15360'],
            'dress_colors' => ['nullable', 'array', 'max:8'],
            'dress_colors.*.name' => ['required_with:dress_colors', 'string', 'max:40'],
            'dress_colors.*.hex' => ['required_with:dress_colors', 'string', 'max:7'],
            'dress_colors.*.note' => ['nullable', 'string', 'max:200'],
            'rsvp_enabled' => ['sometimes', 'boolean'],
            'template' => [
                'nullable',
                'string',
                'max:80',
                Rule::in(array_column(TemplateCatalog::definitions(), 'template')),
            ],
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
            if ($this->filled('invitation_id')) {
                $ownsInvitation = $this->user()
                    ->invitations()
                    ->where('id', $this->integer('invitation_id'))
                    ->exists();

                if (! $ownsInvitation) {
                    $validator->errors()->add('invitation_id', 'Taklifnoma topilmadi.');
                }
            }

            $slug = (string) $this->input('template_slug', 'nikoh');

            foreach (BuilderEventProfile::fieldsForSlug($slug) as $field) {
                if (! $field['required']) {
                    continue;
                }

                if (trim((string) $this->input("profile.{$field['key']}", '')) === '') {
                    $validator->errors()->add("profile.{$field['key']}", __($field['label']).' majburiy.');
                }
            }
        });
    }
}
