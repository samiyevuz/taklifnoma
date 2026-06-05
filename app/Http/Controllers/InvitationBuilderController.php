<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvitationRequest;
use App\Models\Invitation;
use App\Services\InvitationMediaService;
use App\Services\InvitationService;
use App\Services\Rsvp\RsvpDashboardService;
use App\Support\BuilderEventProfile;
use App\Support\InvitationDefaults;
use App\Support\TemplateCatalog;
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
        $defaults['template'] = $catalogTemplate['template'] ?? 'nikoh-premium';
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

        return [
            'template_slug' => $slug,
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
            'music_url' => $source->music_url ?? '',
            'cover_image' => $source->cover_image ?? '',
            'cover_image_url' => filled($source->cover_image ?? null) ? asset($source->cover_image) : '',
            'dress_colors' => $source->dress_colors ?? InvitationDefaults::dressColors(),
            'rsvp_enabled' => $source->rsvp_enabled ?? true,
            'slug' => $invitation?->slug,
            'is_edit' => $invitation !== null,
            'action' => $invitation
                ? route('builder.update', $invitation)
                : route('builder.store'),
            'method' => $invitation ? 'PUT' : 'POST',
            'price_amount' => $template['price_amount'] ?? 89000,
            'currency' => __('landing.currency'),
            'template_title' => $template['title'] ?? 'Nikoh To\'yi Premium',
            'template_blade' => $template['template'] ?? 'nikoh-premium',
            'music_presets' => InvitationDefaults::musicPresets(),
            'default_music_url' => asset('audio/romantic-wedding.mp3'),
            'payments' => [
                'generate_url' => route('payments.invoice.generate'),
                'return_url' => route('payments.return'),
                'site_host' => parse_url(config('app.url'), PHP_URL_HOST) ?: 'taklifnoma.net',
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
