<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Support\InvitationDefaults;
use App\Support\Seo\SeoData;
use App\Support\TemplateCatalog;
use Illuminate\View\View;

class TemplatePreviewController extends Controller
{
    public function show(string $locale, string $templateSlug): View
    {
        $template = TemplateCatalog::find($templateSlug);

        if (! $template) {
            abort(404);
        }

        $invitation = InvitationDefaults::demoInvitation($templateSlug);
        $title = $invitation->displayTitle().' — '.$invitation->event_type;
        $description = SeoData::invitationDescription($invitation);

        return view('templates.nikoh-premium', [
            'invitation' => $invitation,
            'title' => $title,
            'metaDescription' => $description,
            'seo' => SeoData::forPreview($templateSlug, $title, $description),
            'isPreview' => true,
        ]);
    }
}
