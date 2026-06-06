<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvitationRequest;
use App\Models\Invitation;
use App\Services\InvitationMediaService;
use App\Services\InvitationService;
use App\Services\Rsvp\RsvpDashboardService;
use App\Support\BuilderEventProfile;
use App\Support\ComplimentaryAccess;
use App\Support\InvitationDefaults;
use App\Support\TemplateCatalog;
use App\Support\PlanEntitlements;
use App\Support\StoryGallerySlots;
use App\Support\TemplateVariantCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvitationBuilderController extends Controller
{
    public function __construct(
        private readonly InvitationService $invitationService,
        private readonly InvitationMediaService $mediaService,
        private readonly RsvpDashboardService $rsvpDashboardService,
    ) {}

    public function create(Request $request): View
    {
        $slug = (string) $request->query('template', 'nikoh');
        $catalogTemplate = TemplateCatalog::find($slug) ?? TemplateCatalog::find('nikoh');

        $defaults = InvitationDefaults::demoAttributes();
        unset($defaults['slug'], $defaults['status'], $defaults['published_at']);
        $slug = $catalogTemplate['slug'] ?? 'nikoh';
        $defaultVariant = TemplateVariantCatalog::defaultForFamily($slug);
        $defaults['template'] = $defaultVariant['blade'] ?? $catalogTemplate['template'] ?? 'nikoh-premium';
        $defaults['template_variant'] = $defaultVariant['id'] ?? null;
        $defaults['event_type'] = $catalogTemplate['title'] ?? 'Nikoh To\'yi';
        $normalized = BuilderEventProfile::normalizeForStorage($slug, [
            'profile' => BuilderEventProfile::demoProfile($slug),
        ]);
        $defaults['groom_name'] = $normalized['groom_name'];
        $defaults['bride_name'] = $normalized['bride_name'];
        $defaults['profile_meta'] = $normalized['profile_meta'];

        return view('builder.create', [
            'title' => 'Taklifnoma Yaratish — Builder',
            'defaults' => $defaults,
            'bootstrap' => $this->builderBootstrap(null, $defaults, $catalogTemplate),
        ]);
    }

    public function store(StoreInvitationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $invitation = $this->invitationService->create(
            $data,
            $request->boolean('publish')
        );

        $this->mediaService->sync($request, $invitation);

        return redirect()
            ->route('builder.edit', $invitation)
            ->with('success', $request->boolean('publish')
                ? __('builder.saved_published')
                : __('builder.saved_draft'));
    }

    public function edit(Invitation $invitation): View
    {
        $this->authorize('update', $invitation);

        return view('builder.edit', [
            'title' => 'Tahrirlash — '.$invitation->displayTitle(),
            'invitation' => $invitation,
            'rsvpSnapshot' => $this->rsvpDashboardService->snapshot($invitation),
            'bootstrap' => $this->builderBootstrap($invitation),
        ]);
    }

    private function builderBootstrap(?Invitation $invitation, ?array $defaults = null, ?array $catalogTemplate = null): array
    {
        $template = $catalogTemplate
            ?? ($invitation ? TemplateCatalog::findByBlade($invitation->template) : null)
            ?? TemplateCatalog::find('nikoh');
        $source = $invitation ?? (object) ($defaults ?? InvitationDefaults::demoAttributes());
        $slug = $template['slug'] ?? 'nikoh';
        $variants = TemplateVariantCatalog::forFamily($slug);
        $selectedVariantId = $invitation?->template_variant
            ?? (is_array($defaults) ? ($defaults['template_variant'] ?? null) : null);
        $selectedVariant = TemplateVariantCatalog::find($slug, $selectedVariantId)
            ?? TemplateVariantCatalog::defaultForFamily($slug);
        $siteHost = parse_url(config('app.url'), PHP_URL_HOST) ?: 'taklifnoma.net';
        $defaultMusicUrl = asset('audio/romantic-wedding.mp3');
        return [
            'template_slug' => $slug,
            'template_variant' => $selectedVariant['id'] ?? null,
            'template_variants' => $variants,
            'field_schema' => BuilderEventProfile::bootstrapSchema($slug, $source),
            'groom_name' => $source->groom_name ?? '',
            'bride_name' => $source->bride_name ?? '',
            'event_type' => $source->event_type ?? 'Nikoh To\'yi',
            'event_at' => isset($source->event_at)
                ? (\Carbon\Carbon::parse($source->event_at)->format('Y-m-d\TH:i'))
                : now()->addMonths(3)->format('Y-m-d\TH:i'),
            'event_city' => $source->event_city ?? 'Toshkent',
            'venue_name' => $source->venue_name ?? '',
            'venue_address' => $source->venue_address ?? '',
            'map_lat' => $source->map_lat ?? '',
            'map_lng' => $source->map_lng ?? '',
            'invitation_text_1' => $source->invitation_text_1 ?? '',
            'invitation_text_2' => $source->invitation_text_2 ?? '',
            'family_signature' => $source->family_signature ?? '',
            'music_url' => filled($source->music_url ?? null) ? $source->music_url : $defaultMusicUrl,
            'cover_image' => $source->cover_image ?? '',
            'cover_image_url' => filled($source->cover_image ?? null) ? asset($source->cover_image) : '',
            'story_gallery_slots' => StoryGallerySlots::slotsForSlug($slug),
            'story_gallery_title' => StoryGallerySlots::sectionTitle($slug),
            'story_gallery_subtitle' => StoryGallerySlots::sectionSubtitle($slug),
            'story_images' => StoryGallerySlots::hydrate(
                is_array($source->story_images ?? null) ? $source->story_images : null,
                $slug
            ),
            'dress_colors' => $source->dress_colors ?? InvitationDefaults::dressColors(),
            'rsvp_enabled' => $source->rsvp_enabled ?? true,
            'slug' => $invitation?->slug,
            'is_published' => $invitation?->isPublished() ?? false,
            'public_url' => ($invitation && $invitation->isPublished()) ? $invitation->publicUrl() : null,
            'checkout_url_pending' => __('builder.checkout_url_pending'),
            'is_edit' => $invitation !== null,
            'action' => $invitation
                ? route('builder.update', $invitation)
                : route('builder.store'),
            'method' => $invitation ? 'PUT' : 'POST',
            'price_amount' => $selectedVariant['price_amount'] ?? $template['price_amount'] ?? 89000,
            'currency' => __('landing.currency'),
            'template_title' => $selectedVariant['title'] ?? $template['title'] ?? 'Nikoh To\'yi Premium',
            'template_blade' => $selectedVariant['blade'] ?? $template['template'] ?? 'nikoh-premium',
            'plan_tier' => $selectedVariant['entitlements']['tier'] ?? $invitation?->plan_tier,
            'plan_entitlements' => $selectedVariant['entitlements'] ?? PlanEntitlements::forTheme('premium'),
            'guest_limit' => $selectedVariant['guest_limit'] ?? null,
            'guest_limit_label' => $selectedVariant['guest_limit_label'] ?? '',
            'slug_host' => $siteHost,
            'music_presets' => InvitationDefaults::musicPresets(),
            'default_music_url' => asset('audio/romantic-wedding.mp3'),
            'payments' => [
                'generate_url' => route('payments.invoice.generate'),
                'return_url' => route('payments.return'),
                'site_host' => parse_url(config('app.url'), PHP_URL_HOST) ?: 'taklifnoma.net',
                'complimentary' => auth()->check() && ComplimentaryAccess::hasAccess(auth()->user()),
            ],
        ];
    }

    public function update(StoreInvitationRequest $request, Invitation $invitation): RedirectResponse
    {
        $this->authorize('update', $invitation);

        $publish = $request->has('publish')
            ? $request->boolean('publish')
            : null;

        $this->invitationService->update($invitation, $request->validated(), $publish);
        $this->mediaService->sync($request, $invitation);

        return redirect()
            ->route('builder.edit', $invitation)
            ->with('success', __('builder.saved_changes'));
    }
}
