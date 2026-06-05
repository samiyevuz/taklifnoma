<?php

use App\Http\Controllers\Account\DashboardController;
use App\Http\Controllers\Account\FavoriteController;
use App\Http\Controllers\Account\OrderController;
use App\Http\Controllers\Account\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\InvitationBuilderController;
use App\Http\Controllers\InvitationViewController;
use App\Http\Controllers\RsvpController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.landing', [
        'title' => 'Taklifnoma — Premium Raqamli Taklifnomalar',
        'metaDescription' => "Hayotingizdagi eng go'zal kun uchun mukammal raqamli taklifnomalar. Jonli RSVP, fon musiqasi va premium shablonlar.",
        'favoriteSlugs' => auth()->check() ? auth()->user()->favoriteSlugs() : [],
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

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', LogoutController::class)->name('logout');

    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders');
        Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites');
    });

    Route::post('/favorites/{templateSlug}', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{templateSlug}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');

    Route::prefix('builder')->name('builder.')->group(function () {
        Route::get('/create', [InvitationBuilderController::class, 'create'])->name('create');
        Route::post('/', [InvitationBuilderController::class, 'store'])->name('store');
        Route::get('/{invitation}/edit', [InvitationBuilderController::class, 'edit'])->name('edit');
        Route::put('/{invitation}', [InvitationBuilderController::class, 'update'])->name('update');
    });
});
