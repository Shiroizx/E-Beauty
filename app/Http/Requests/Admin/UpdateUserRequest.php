<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $pw = $this->input('password');
        if ($pw !== null && is_string($pw) && trim($pw) === '') {
            $this->merge([
                'password' => null,
                'password_confirmation' => null,
            ]);
        }
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        $passwordRules = ['nullable', 'string'];
        $pw = $this->input('password');
        if (is_string($pw) && trim($pw) !== '') {
            $passwordRules[] = 'confirmed';
            $passwordRules[] = Password::defaults();
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => $passwordRules,
            'role' => ['required', 'string', Rule::in(['customer', 'admin', 'super_admin'])],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
        ];
    }
}
