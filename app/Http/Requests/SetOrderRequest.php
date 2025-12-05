<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SetOrderRequest extends FormRequest
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

            'items' => 'required|array|min:1',
            'items.*.service_id' => 'required|exists:services,id',
            'items.*.number_of_workers' => 'required|integer|max:5|min:1',

            'is_direct_service' => 'required|boolean',
            'reserve_datetime' => [Rule::requiredIf(request('is_direct_service') == false), Rule::date()->after(now())],
            'address_id' => 'required|exists:location_addresses,id',
            'images' => 'array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'payment_method' => 'required|in:cash,card',
            'description' => 'nullable|string|max:255',
            'coupon' => 'nullable|string|max:255',
        ];
    }
}
