<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer id
 * @property integer product_id
 * @property Product product
 * @property string  country_code
 * @property string  currency_code
 * @property double  price
 * @property string  start_date
 * @property string  end_date
 * @property integer priority
 */
class PriceList extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'product_id',
        'country_code',
        'currency_code',
        'price',
        'start_date',
        'end_date',
        'priority',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'double'
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }

    public function scopeApplicablePrice(Builder|PriceList $query, string $countryCode, string $currencyCode, string $date): Builder|PriceList
    {
        return $query->selectRaw(
            "price as price_list_price,
        CASE
            WHEN country_code = ? AND currency_code = ? AND start_date IS NOT NULL AND end_date IS NOT NULL THEN 1
            WHEN country_code = ? AND currency_code = ? AND start_date IS NULL AND end_date IS NULL THEN 2
            WHEN country_code = ? AND currency_code IS NULL AND start_date IS NOT NULL AND end_date IS NOT NULL THEN 3
            WHEN country_code = ? AND currency_code IS NULL AND start_date IS NULL AND end_date IS NULL THEN 4
            WHEN country_code IS NULL AND currency_code = ? AND start_date IS NOT NULL AND end_date IS NOT NULL THEN 5
            WHEN country_code IS NULL AND currency_code = ? AND start_date IS NULL AND end_date IS NULL THEN 6
            ELSE 7
        END AS specificity",
            [
                $countryCode,
                $currencyCode,
                $countryCode,
                $currencyCode,
                $countryCode,
                $countryCode,
                $currencyCode,
                $currencyCode,
            ]
        )
            ->addSelect(['specificity']) // Add this column to ensure it exists
            ->where(function (Builder|PriceList $query) use ($countryCode) {
                $query->where('country_code', $countryCode)
                    ->orWhereNull('country_code');
            })
            ->where(function (Builder|PriceList $query) use ($currencyCode) {
                $query->where('currency_code', $currencyCode)
                    ->orWhereNull('currency_code');
            })
            ->where(function (Builder|PriceList $query) use ($date) {
                $query->where(function (Builder $query) use ($date) {
                    $query->where('start_date', '<=', $date)
                        ->where('end_date', '>=', $date);
                })->orWhere(function (Builder $query) {
                    $query->whereNull('start_date')
                        ->whereNull('end_date');
                });
            })
            ->orderBy('specificity', 'asc')
            ->orderBy('priority', 'asc');
    }
}
