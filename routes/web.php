<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.ui-kit-preview', [
        'title' => 'Taklifnoma — Premium UI Kit',
        'metaDescription' => "To'yingizga raqamli taklifnoma yarating. O'zbekistondagi eng zamonaviy onlayn taklifnoma platformasi.",
    ]);
});

Route::get('/ui-kit', function () {
    return view('pages.ui-kit-preview', [
        'title' => 'Premium UI Kit Preview — Taklifnoma',
    ]);
});
