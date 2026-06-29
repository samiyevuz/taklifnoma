<?php

namespace App\Http\Controllers;

use App\Support\InvitationResolver;
use App\Support\Seo\SeoData;
use Illuminate\View\View;

class InvitationViewController extends Controller
{
    public function show(string $locale, string $slug): View
    {
        return $this->render(InvitationResolver::findPublic($slug));
    }

    public function render($invitation): View
    {
        $blade = $invitation->template ?: 'nikoh-premium';
        $view = view()->exists("templates.{$blade}")
            ? "templates.{$blade}"
            : 'templates.nikoh-premium';

        $title = $invitation->coupleTitle().' — '.$invitation->event_type;
        $description = SeoData::invitationDescription($invitation);

        return view($view, [
            'invitation' => $invitation,
            'title' => $title,
            'metaDescription' => $description,
            'seo' => SeoData::forInvitation($invitation, $title, $description),
        ]);
    }
}
