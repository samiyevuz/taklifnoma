<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Ismingizni kiriting.',
            'email.required' => 'Email manzilini kiriting.',
            'email.unique' => 'Bu email allaqachon ro\'yxatdan o\'tgan.',
            'password.required' => 'Parol kiriting.',
            'password.confirmed' => 'Parollar mos kelmadi.',
        ];
    }
}
