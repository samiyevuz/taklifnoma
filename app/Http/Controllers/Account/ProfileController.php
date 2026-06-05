<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\UpdatePasswordRequest;
use App\Http\Requests\Account\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('account.profile', [
            'title' => 'Profil — Taklifnoma',
            'user' => auth()->user(),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        auth()->user()->update($request->validated());

        return back()->with('success', 'Profil ma\'lumotlari yangilandi.');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        auth()->user()->update([
            'password' => $request->validated('password'),
        ]);

        return back()->with('success', 'Parol muvaffaqiyatli yangilandi.');
    }
}
