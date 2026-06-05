<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvitationRequest;
use App\Models\Invitation;
use App\Services\InvitationService;
use App\Support\InvitationDefaults;
use App\Support\TemplateCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InvitationBuilderController extends Controller
{
    public function __construct(
        private readonly InvitationService $invitationService,
    ) {}

    public function create(): View
    {
        $defaults = InvitationDefaults::demoAttributes();
        unset($defaults['slug'], $defaults['status'], $defaults['published_at']);

        return view('builder.create', [
            'title' => 'Taklifnoma Yaratish — Builder',
            'defaults' => $defaults,
            'bootstrap' => $this->builderBootstrap(null, $defaults),
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
            'title' => 'Tahrirlash — '.$invitation->coupleTitle(),
            'invitation' => $invitation,
            'stats' => $invitation->rsvpStats(),
            'bootstrap' => $this->builderBootstrap($invitation),
        ]);
    }

    private function builderBootstrap(?Invitation $invitation, ?array $defaults = null): array
    {
        $template = TemplateCatalog::find('nikoh');
        $source = $invitation ?? (object) ($defaults ?? InvitationDefaults::demoAttributes());

        return [
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
            'music_presets' => InvitationDefaults::musicPresets(),
            'default_music_url' => asset('audio/romantic-wedding.mp3'),
        ];
    }

    public function update(StoreInvitationRequest $request, Invitation $invitation): RedirectResponse
    {
        $this->authorize('update', $invitation);

        $publish = $request->has('publish')
            ? $request->boolean('publish')
            : null;

        $this->invitationService->update($invitation, $request->validated(), $publish);

        return redirect()
            ->route('builder.edit', $invitation)
            ->with('success', __('builder.saved_changes'));
    }
}
