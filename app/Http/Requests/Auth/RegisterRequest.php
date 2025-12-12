<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Propaganistas\LaravelPhone\Rules\Phone;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone_number' => ['required', 'string', '', 'unique:mobile_users,phone_number', (new Phone)->international()->country([config('app.supported_countries')])],
            'password' => ['required', 'string', 'min:8'],
            'full_name' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            // 'home_phone' => ['nullable', 'string', 'max:255'],
            // 'email' => ['nullable', 'email', 'max:255', 'unique:mobile_users,email'],
        ];
    }
}
