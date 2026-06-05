<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Support\InvitationDefaults;
use App\Support\TemplateCatalog;
use Illuminate\View\View;

class TemplatePreviewController extends Controller
{
    public function show(string $templateSlug): View
    {
        $template = TemplateCatalog::find($templateSlug);

        if (! $template) {
            abort(404);
        }

        $invitation = InvitationDefaults::demoInvitation($templateSlug);

        return view('templates.nikoh-premium', [
            'invitation' => $invitation,
            'title' => $invitation->displayTitle().' — '.$invitation->event_type,
            'metaDescription' => $invitation->displayTitle().' '.$invitation->event_type.' taklifnomasi.',
            'isPreview' => true,
        ]);
    }
}
