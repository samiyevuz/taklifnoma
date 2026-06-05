<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.landing', [
        'title' => 'Taklifnoma — Premium Raqamli Taklifnomalar',
        'metaDescription' => "Hayotingizdagi eng go'zal kun uchun mukammal raqamli taklifnomalar. Jonli RSVP, fon musiqasi va premium shablonlar.",
    ]);
});

Route::get('/ui-kit', function () {
    return view('pages.ui-kit-preview', [
        'title' => 'Premium UI Kit Preview — Taklifnoma',
    ]);
});

Route::get('/invite/nikoh-premium', function () {
    return view('templates.nikoh-premium', [
        'title' => 'Ali & Vali — Nikoh To\'yi',
        'metaDescription' => 'Ali va Vali nikoh to\'yi taklifnomasi. 22 Sentabr 2026, Toshkent.',
    ]);
});
