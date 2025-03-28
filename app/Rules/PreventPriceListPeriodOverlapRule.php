<?php

namespace App\Rules;

use App\Models\PriceList;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class PreventPriceListPeriodOverlapRule implements ValidationRule
{
    private int $productId;
    private ?string $countryCode;
    private ?string $currencyCode;
    private ?string $startDate;
    private ?string $endDate;
    private ?int $priority;

    /**
     */
    public function __construct()
    {
        $this->productId = request()->input('product_id');
        $this->countryCode = request()->input('country_code');
        $this->currencyCode = request()->input('currency_code');
        $this->startDate = request()->input('start_date');
        $this->endDate = request()->input('end_date');
        $this->priority = request()->input('priority');
    }

    /**
     * Run the validation rule.
     * @param Closure(string, ?string=): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $overlap = PriceList::where('product_id', $this->productId)
            ->where('country_code', $this->countryCode)
            ->where('currency_code', $this->currencyCode)
            ->where('priority', $this->priority)
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('start_date', '<=', $this->endDate)
                        ->where('end_date', '>=', $this->startDate);
                })->orWhere(function ($query) {
                    $query->where('start_date', '<=', $this->startDate)
                        ->where('end_date', '>=', $this->endDate);
                });
            })
            ->exists();

        if ($overlap) {
            $fail("The selected period overlap with another price list period");
        }
    }
}
