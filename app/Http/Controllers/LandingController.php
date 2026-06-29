<?php

namespace App\Http\Controllers;

use App\Support\Seo\SeoData;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function __invoke(): View
    {
        $title = __('landing.meta_title');
        $description = __('landing.meta_description');

        return view('pages.landing', [
            'title' => $title,
            'metaDescription' => $description,
            'seo' => SeoData::forLanding($title, $description),
            'favoriteSlugs' => auth()->check() ? auth()->user()->favoriteSlugs() : [],
        ]);
    }
}
