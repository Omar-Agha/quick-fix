<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CreateOrUpdateCompanyRequest extends FormRequest
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
            'id' => 'nullable|exists:companies,id',
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'password' => Password::required()
        ];
    }
}
