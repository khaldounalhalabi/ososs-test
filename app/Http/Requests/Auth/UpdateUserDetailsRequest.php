<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserDetailsRequest extends FormRequest
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

            'email' => 'email|max:255|min:3|string|unique:users,email,' . auth()->user()?->id,
            'name' => 'string|max:255|min:3',
            'country_id' => ['integer', 'exists:countries,id', Rule::excludeIf(fn() => auth()->user()->isAdmin()), 'nullable', Rule::requiredIf(fn() => auth()->user()->isCustomer())],
        ];
    }
}
