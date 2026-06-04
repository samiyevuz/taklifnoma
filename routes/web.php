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
