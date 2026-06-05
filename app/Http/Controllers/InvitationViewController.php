<?php

namespace App\Http\Controllers;

use App\Support\InvitationResolver;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvitationViewController extends Controller
{
    public function show(Request $request, string $slug): View
    {
        return $this->render(InvitationResolver::findPublic($slug));
    }

    public function render($invitation): View
    {
        $blade = $invitation->template ?: 'nikoh-premium';
        $view = view()->exists("templates.{$blade}")
            ? "templates.{$blade}"
            : 'templates.nikoh-premium';

        return view($view, [
            'invitation' => $invitation,
            'title' => $invitation->coupleTitle().' — '.$invitation->event_type,
            'metaDescription' => $invitation->coupleTitle().' '.$invitation->event_type.' taklifnomasi.',
        ]);
    }
}
