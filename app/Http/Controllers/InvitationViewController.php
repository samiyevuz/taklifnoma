<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvitationViewController extends Controller
{
    public function show(Request $request, string $slug): View
    {
        $invitation = Invitation::query()
            ->where('slug', $slug)
            ->where('status', Invitation::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->firstOrFail();

        return view('templates.nikoh-premium', [
            'invitation' => $invitation,
            'title' => $invitation->coupleTitle().' — '.$invitation->event_type,
            'metaDescription' => $invitation->coupleTitle().' '.$invitation->event_type.' taklifnomasi.',
        ]);
    }
}
