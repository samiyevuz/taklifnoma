<?php

namespace App\Http\Requests;

use App\Support\BuilderEventProfile;
use App\Support\CustomDomainFormatter;
use App\Support\TemplateCatalog;
use App\Support\PlanEntitlements;
use App\Support\StoryGallerySlots;
use App\Support\TemplateVariantCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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

        $slug = $this->resolveTemplateSlug();
        $normalized = BuilderEventProfile::normalizeForStorage($slug, $this->all());

        $this->merge([
            'groom_name' => $normalized['groom_name'],
            'bride_name' => $normalized['bride_name'],
            'profile_meta' => $normalized['profile_meta'],
        ]);

        $this->mergeCustomDomain($slug);
    }

    private function mergeCustomDomain(string $templateSlug): void
    {
        if (! $this->has('custom_domain_subdomain')) {
            return;
        }

        $subdomain = trim((string) $this->input('custom_domain_subdomain'));

        $this->merge([
            'custom_domain' => $subdomain !== ''
                ? CustomDomainFormatter::assemble($templateSlug, $subdomain)
                : null,
        ]);
    }

    public function rules(): array
    {
        $slug = $this->resolveTemplateSlug();
        $rules = [
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
            'story_caption_primary' => ['nullable', 'string', 'max:120'],
            'story_caption_secondary' => ['nullable', 'string', 'max:120'],
            'story_caption_moment_0' => ['nullable', 'string', 'max:120'],
            'story_caption_moment_1' => ['nullable', 'string', 'max:120'],
            'story_caption_moment_2' => ['nullable', 'string', 'max:120'],
            'story_image_primary' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'story_image_secondary' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'story_image_moment_0' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'story_image_moment_1' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'story_image_moment_2' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
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
            'template_slug' => ['nullable', 'string', Rule::in(TemplateCatalog::slugs())],
            'template_variant' => ['nullable', 'string', 'max:80'],
            'custom_domain' => ['nullable', 'string', 'max:120', 'regex:/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,}$/i'],
            'custom_domain_subdomain' => ['nullable', 'string', 'max:63', 'regex:/^[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/i'],
            'template' => [
                'nullable',
                'string',
                'max:80',
                Rule::in(array_column(TemplateCatalog::definitions(), 'template')),
            ],
        ];

        foreach (BuilderEventProfile::fieldsForSlug($slug) as $field) {
            $rules["profile.{$field['key']}"] = [
                $field['required'] ? 'required' : 'nullable',
                'string',
                'max:80',
            ];
        }

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $slug = $this->resolveTemplateSlug();

            foreach (BuilderEventProfile::fieldsForSlug($slug) as $field) {
                if (! $field['required']) {
                    continue;
                }

                $value = trim((string) $this->input("profile.{$field['key']}", ''));

                if ($value === '') {
                    $validator->errors()->add(
                        "profile.{$field['key']}",
                        __($field['label']).' majburiy.'
                    );
                }
            }

            if ($this->filled('template_variant')) {
                $variant = TemplateVariantCatalog::find($slug, $this->input('template_variant'));

                if (! $variant) {
                    $validator->errors()->add('template_variant', 'Tanlangan shablon varianti noto\'g\'ri.');
                }
            }

            foreach (PlanEntitlements::validatePayload($this->all(), $this->route('invitation')) as $field => $message) {
                $validator->errors()->add($field, $message);
            }

            $plan = PlanEntitlements::forVariant($slug, $this->input('template_variant'));
            if (! $plan['story_gallery']) {
                foreach (StoryGallerySlots::slotKeys() as $slotKey) {
                    if ($this->hasFile("story_image_{$slotKey}")) {
                        $validator->errors()->add(
                            "story_image_{$slotKey}",
                            'Rasm galereyasi faqat Luxury va Royal VIP tariflarida mavjud.'
                        );
                        break;
                    }
                }
            }

            if ($this->filled('custom_domain')) {
                $domain = strtolower($this->input('custom_domain'));
                $appHost = parse_url(config('app.url'), PHP_URL_HOST);

                if ($appHost && $domain === strtolower($appHost)) {
                    $validator->errors()->add('custom_domain', 'Asosiy platforma domenidan foydalanib bo\'lmaydi.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'event_at.required' => 'Tadbir sanasi majburiy.',
            'venue_name.required' => 'Joy nomi majburiy.',
            'venue_address.required' => 'Manzil majburiy.',
            'invitation_text_1.required' => 'Taklifnoma matni majburiy.',
        ];
    }

    private function resolveTemplateSlug(): string
    {
        $template = (string) $this->input('template', '');
        $catalog = TemplateCatalog::findByBlade($template);

        return $catalog['slug'] ?? 'nikoh';
    }
}
