<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->hasPermission('users.create') ?? false; }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'password' => ['required', 'string', 'min:10', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
            'locale' => ['required', Rule::in(['en', 'ar'])],
            'role_ids' => ['array'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
        ];
    }
}
