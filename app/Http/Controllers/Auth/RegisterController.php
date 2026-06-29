<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Support\Seo\SeoData;
use App\Models\User;
use App\Support\ComplimentaryAccess;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register', [
            'title' => 'Ro\'yxatdan o\'tish — Taklifnoma',
            'seo' => SeoData::noindex('Ro\'yxatdan o\'tish — Taklifnoma'),
        ]);
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            ...$request->validated(),
            'role' => User::ROLE_USER,
            'is_complimentary' => ComplimentaryAccess::emailMatches($request->input('email')),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()
            ->route('account.dashboard')
            ->with('success', __('auth.welcome'));
    }
}
