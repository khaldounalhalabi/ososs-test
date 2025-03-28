<?php

namespace App\Http\Requests;

use App\Rules\PreventPriceListPeriodOverlapRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdatePriceListRequest extends FormRequest
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
            'product_id' => 'required|integer|exists:products,id',
            'country_code' => [
                'nullable',
                'string',
                'exists:countries,code',
                Rule::unique('price_lists', 'country_code')
                    ->where('currency_code', $this->input('currency_code'))
                    ->where('product_id', $this->input('product_id'))
                    ->where('start_date', $this->input('start_date'))
                    ->where('end_date', $this->input('end_date'))
                    ->where('priority', $this->input('priority'))
            ],
            'currency_code' => [
                'nullable', 'string', 'exists:currencies,code',
                Rule::unique('price_lists', 'currency_code')
                    ->where('country_code', $this->input('country_code'))
                    ->where('product_id', $this->input('product_id'))
                    ->where('start_date', $this->input('start_date'))
                    ->where('end_date', $this->input('end_date'))
                    ->where('priority', $this->input('priority'))
            ],
            'price' => ['required', 'numeric', 'min:0', 'max:100000000'],
            'start_date' => ['nullable', 'date', 'date_format:Y-m-d', 'required_with:end_date'],
            'end_date' => ['nullable', 'date', 'date_format:Y-m-d', 'after:start_date', 'required_with:start_date', new PreventPriceListPeriodOverlapRule()],
            'priority' => [
                'nullable',
                'integer',
                'min:0',
                'max:100000',
                Rule::unique('price_lists', 'priority')
                    ->where('product_id', $this->input('product_id'))
                    ->where('country_code', $this->input('country_code'))
                    ->where('currency_code', $this->input('currency_code'))
                    ->where('start_date', $this->input('start_date'))
                    ->where('end_date', $this->input('end_date'))
            ],
        ];
    }
}
