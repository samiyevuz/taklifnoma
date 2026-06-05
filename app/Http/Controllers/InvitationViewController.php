<?php

namespace App\Http\Controllers;

use App\Support\InvitationResolver;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvitationViewController extends Controller
{
    public function show(Request $request, string $slug): View
    {
        $invitation = InvitationResolver::findPublic($slug);

        return view('templates.nikoh-premium', [
            'invitation' => $invitation,
            'title' => $invitation->coupleTitle().' — '.$invitation->event_type,
            'metaDescription' => $invitation->coupleTitle().' '.$invitation->event_type.' taklifnomasi.',
        ]);
    }
}
