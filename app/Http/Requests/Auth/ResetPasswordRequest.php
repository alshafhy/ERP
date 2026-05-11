<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => [
                'required',
                'confirmed',
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
            'password.required' => 'يرجى إدخال كلمة المرور الجديدة',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
            'password.min' => 'يجب أن لا يقل عدد أحرف كلمة المرور عن 8 أحرف',
            'password.max' => 'يجب أن لا يزيد عدد أحرف كلمة المرور عن 25 حرف',
            'password.mixed' => 'يجب أن تحتوي كلمة المرور على حرف كبير وحرف صغير على الأقل',
            'password.numbers' => 'يجب أن تحتوي كلمة المرور على رقم واحد على الأقل',
            'password.symbols' => 'يجب أن تحتوي كلمة المرور على رمز واحد على الأقل',
        ];
    }
}
