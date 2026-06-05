<?php

use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\InvitationController as AdminInvitationController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\RsvpController as AdminRsvpController;
use App\Http\Controllers\Admin\TemplateController as AdminTemplateController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Account\DashboardController;
use App\Http\Controllers\Account\FavoriteController;
use App\Http\Controllers\Account\OrderController;
use App\Http\Controllers\Account\ProfileController;
use App\Http\Controllers\Account\RsvpPanelController;
use App\Http\Controllers\Account\TelegramController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\InvitationBuilderController;
use App\Http\Controllers\InvitationViewController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RsvpController;
use App\Http\Controllers\TemplatePreviewController;
use App\Http\Controllers\TelegramWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::get('/', function () {
    return view('pages.landing', [
        'title' => __('landing.meta_title'),
        'metaDescription' => __('landing.meta_description'),
        'favoriteSlugs' => auth()->check() ? auth()->user()->favoriteSlugs() : [],
    ]);
});

Route::get('/ui-kit', function () {
    return view('pages.ui-kit-preview', [
        'title' => 'Premium UI Kit Preview — Taklifnoma',
    ]);
});

Route::get('/preview/{templateSlug}', [TemplatePreviewController::class, 'show'])->name('template.preview');
Route::get('/l/{slug}', [InvitationViewController::class, 'show'])->name('invitation.show');
Route::get('/i/{slug}', [InvitationViewController::class, 'show']);
Route::post('/l/{slug}/rsvp', [RsvpController::class, 'store'])->name('rsvp.store');
Route::post('/i/{slug}/rsvp', [RsvpController::class, 'store']);

Route::post('/payments/webhooks/payme', [PaymentController::class, 'handlePaymeWebhook'])->name('payments.webhooks.payme');
Route::post('/payments/webhooks/click', [PaymentController::class, 'handleClickWebhook'])->name('payments.webhooks.click');
Route::get('/payments/return', [PaymentController::class, 'return'])->name('payments.return');
Route::post('/telegram/webhook', [TelegramWebhookController::class, 'handle'])->name('telegram.webhook');

Route::redirect('/invite/nikoh-premium', '/preview/nikoh');
Route::redirect('/i/farhod-shirin', '/preview/nikoh');
Route::redirect('/l/farhod-shirin', '/preview/nikoh');

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
        Route::get('/telegram/connect', [TelegramController::class, 'connect'])->name('telegram.connect');
        Route::post('/telegram/disconnect', [TelegramController::class, 'disconnect'])->name('telegram.disconnect');
        Route::post('/telegram/toggle', [TelegramController::class, 'toggle'])->name('telegram.toggle');
    });

    Route::post('/favorites/{templateSlug}', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{templateSlug}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');

    Route::post('/payments/invoice', [PaymentController::class, 'generateInvoice'])->name('payments.invoice.generate');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::get('/stats', [AdminDashboardController::class, 'stats'])->name('stats');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::get('/invitations', [AdminInvitationController::class, 'index'])->name('invitations.index');
        Route::get('/invitations/{invitation}', [AdminInvitationController::class, 'show'])->name('invitations.show');
        Route::patch('/invitations/{invitation}/status', [AdminInvitationController::class, 'updateStatus'])->name('invitations.status');
        Route::delete('/invitations/{invitation}', [AdminInvitationController::class, 'destroy'])->name('invitations.destroy');
        Route::get('/rsvps', [AdminRsvpController::class, 'index'])->name('rsvps.index');
        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::get('/templates', [AdminTemplateController::class, 'index'])->name('templates.index');
        Route::get('/templates/{eventTemplate}/edit', [AdminTemplateController::class, 'edit'])->name('templates.edit');
        Route::put('/templates/{eventTemplate}', [AdminTemplateController::class, 'update'])->name('templates.update');
        Route::get('/faqs', [AdminFaqController::class, 'index'])->name('faqs.index');
        Route::get('/faqs/create', [AdminFaqController::class, 'create'])->name('faqs.create');
        Route::post('/faqs', [AdminFaqController::class, 'store'])->name('faqs.store');
        Route::put('/faqs/meta', [AdminFaqController::class, 'updateMeta'])->name('faqs.meta');
        Route::get('/faqs/{faq}/edit', [AdminFaqController::class, 'edit'])->name('faqs.edit');
        Route::put('/faqs/{faq}', [AdminFaqController::class, 'update'])->name('faqs.update');
        Route::delete('/faqs/{faq}', [AdminFaqController::class, 'destroy'])->name('faqs.destroy');
        Route::get('/contact', [AdminContactController::class, 'edit'])->name('contact.edit');
        Route::put('/contact', [AdminContactController::class, 'update'])->name('contact.update');
    });

    Route::prefix('builder')->name('builder.')->group(function () {
        Route::get('/create', [InvitationBuilderController::class, 'create'])->name('create');
        Route::post('/', [InvitationBuilderController::class, 'store'])->name('store');
        Route::get('/{invitation}/edit', [InvitationBuilderController::class, 'edit'])->name('edit');
        Route::get('/{invitation}/rsvp/live', [RsvpPanelController::class, 'show'])->name('rsvp.live');
        Route::put('/{invitation}', [InvitationBuilderController::class, 'update'])->name('update');
    });
});
