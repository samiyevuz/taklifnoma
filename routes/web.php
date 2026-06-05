<?php

use App\Http\Controllers\InvitationBuilderController;
use App\Http\Controllers\InvitationViewController;
use App\Http\Controllers\RsvpController;
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

Route::get('/i/{slug}', [InvitationViewController::class, 'show'])->name('invitation.show');
Route::post('/i/{slug}/rsvp', [RsvpController::class, 'store'])->name('rsvp.store');

Route::redirect('/invite/nikoh-premium', '/i/farhod-shirin');

Route::prefix('builder')->name('builder.')->group(function () {
    Route::get('/create', [InvitationBuilderController::class, 'create'])->name('create');
    Route::post('/', [InvitationBuilderController::class, 'store'])->name('store');
    Route::get('/{invitation}/edit', [InvitationBuilderController::class, 'edit'])->name('edit');
    Route::put('/{invitation}', [InvitationBuilderController::class, 'update'])->name('update');
});
