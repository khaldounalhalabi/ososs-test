<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|min:3',
            'email' => 'required|string|email|min:3|max:255|unique:users,email',
            'password' => 'required|string|min:8|max:255|confirmed',
            'country_id' => 'nullable|exists:countries,id|integer',
            'currency_id' => 'nullable|exists:currencies,id|integer',
        ];
    }
}
