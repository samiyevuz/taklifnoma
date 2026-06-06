<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class LandingController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.landing', [
            'title' => __('landing.meta_title'),
            'metaDescription' => __('landing.meta_description'),
            'favoriteSlugs' => auth()->check() ? auth()->user()->favoriteSlugs() : [],
        ]);
    }
}
