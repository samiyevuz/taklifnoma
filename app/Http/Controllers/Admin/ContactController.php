<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function edit(): View
    {
        return view('admin.contact.edit', [
            'title' => __('admin.contact_title'),
            'contact' => $this->contactForForm(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'instagram' => ['nullable', 'url', 'max:255'],
            'telegram' => ['nullable', 'url', 'max:255'],
            'youtube' => ['nullable', 'url', 'max:255'],
            'facebook' => ['nullable', 'url', 'max:255'],
            'whatsapp' => ['nullable', 'url', 'max:255'],
        ]);

        foreach ($validated as $key => $value) {
            SiteSetting::setValue("contact.{$key}", filled($value) ? trim((string) $value) : null);
        }

        return redirect()
            ->route('admin.contact.edit')
            ->with('success', __('admin.contact_updated'));
    }

    private function contactForForm(): array
    {
        $keys = ['email', 'phone', 'instagram', 'telegram', 'youtube', 'facebook', 'whatsapp'];
        $contact = [];

        foreach ($keys as $key) {
            $contact[$key] = SiteSetting::getValue("contact.{$key}") ?? config("social.{$key}");
        }

        return $contact;
    }
}
