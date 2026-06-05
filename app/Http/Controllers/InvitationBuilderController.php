<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvitationRequest;
use App\Models\Invitation;
use App\Services\InvitationService;
use App\Support\InvitationDefaults;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InvitationBuilderController extends Controller
{
    public function __construct(
        private readonly InvitationService $invitationService,
    ) {}

    public function create(): View
    {
        return view('builder.create', [
            'title' => 'Taklifnoma Yaratish — Builder',
            'defaults' => InvitationDefaults::demoAttributes(),
        ]);
    }

    public function store(StoreInvitationRequest $request): RedirectResponse
    {
        $invitation = $this->invitationService->create(
            $request->validated(),
            $request->boolean('publish')
        );

        return redirect()
            ->route('builder.edit', $invitation)
            ->with('success', $request->boolean('publish')
                ? 'Taklifnoma muvaffaqiyatli nashr qilindi!'
                : 'Taklifnoma qoralama sifatida saqlandi.');
    }

    public function edit(Invitation $invitation): View
    {
        return view('builder.edit', [
            'title' => 'Tahrirlash — '.$invitation->coupleTitle(),
            'invitation' => $invitation,
            'stats' => $invitation->rsvpStats(),
        ]);
    }

    public function update(StoreInvitationRequest $request, Invitation $invitation): RedirectResponse
    {
        $publish = $request->has('publish')
            ? $request->boolean('publish')
            : null;

        $this->invitationService->update($invitation, $request->validated(), $publish);

        return redirect()
            ->route('builder.edit', $invitation)
            ->with('success', 'O\'zgarishlar saqlandi.');
    }
}
