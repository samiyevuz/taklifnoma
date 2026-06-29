<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Support\Seo\SeoData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login', [
            'title' => 'Kirish — Taklifnoma',
            'seo' => SeoData::noindex('Kirish — Taklifnoma'),
        ]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => __('auth.invalid_credentials')]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        return redirect()->intended(
            $user && $user->isAdmin()
                ? route('admin.dashboard')
                : route('account.dashboard')
        );
    }
}
