<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateCountryRequest extends FormRequest
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
            'name' => 'required|string|min:3|max:255',
            'code' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('countries', 'code')
                    ->when(
                        $this->method() == 'PUT',
                        fn($rule) => $rule->ignore($this->route('country'))
                    )
            ],
        ];
    }
}
