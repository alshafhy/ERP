<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'password' => [
                'required',
                'string',
                'max:25',
                \Illuminate\Validation\Rules\Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'يرجى إدخال البريد الإلكتروني',
            'email.email' => 'يرجى إدخال بريد إلكتروني بصيغة صحيحة.',
            'email.exists' => 'البريد الإلكتروني غير مسجل في النظام.',
            'password.required' => 'يرجى إدخال كلمة المرور',
            'password' => 'كلمة المرور المدخلة غير صحيحة',
            'password.min' => 'كلمة المرور المدخلة غير صحيحة',
            'password.max' => 'كلمة المرور المدخلة غير صحيحة',
            'password.mixed' => 'كلمة المرور المدخلة غير صحيحة',
            'password.letters' => 'كلمة المرور المدخلة غير صحيحة',
            'password.numbers' => 'كلمة المرور المدخلة غير صحيحة',
            'password.symbols' => 'كلمة المرور المدخلة غير صحيحة',
        ];
    }
}
