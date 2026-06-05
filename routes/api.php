<?php

use App\Http\Controllers\Api\InvitationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.')->group(function () {
    Route::get('invitations/{slug}', [InvitationController::class, 'show'])
        ->name('invitations.show');

    Route::middleware('auth')->group(function () {
        Route::post('invitations', [InvitationController::class, 'store'])
            ->name('invitations.store');

        Route::put('invitations/{invitation:uuid}', [InvitationController::class, 'update'])
            ->name('invitations.update');
    });
});
