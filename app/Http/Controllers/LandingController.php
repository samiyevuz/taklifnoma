<?php

namespace App\Http\Controllers;

use App\Support\CustomDomainResolver;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function __invoke(Request $request): View
    {
        $customInvitation = CustomDomainResolver::findForRequest($request);

        if ($customInvitation) {
            return app(InvitationViewController::class)->render($customInvitation);
        }

        return view('pages.landing', [
            'title' => __('landing.meta_title'),
            'metaDescription' => __('landing.meta_description'),
            'favoriteSlugs' => auth()->check() ? auth()->user()->favoriteSlugs() : [],
        ]);
    }
}
