<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer id
 * @property string  name
 * @property double  base_price
 * @property string  description
 */
class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'base_price',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'double'
        ];
    }

    public function priceLists(): HasMany
    {
        return $this->hasMany(PriceList::class);
    }

    public function scopeWithApplicablePrice(Builder $query): Builder
    {
        $countryCode = request('country_code', auth()->user()->country->code);
        $currencyCode = request('currency_code', auth()->user()->currency->code);
        $date = request('date', now()->format('Y-m-d'));

        return $query->selectRaw("*,
                coalesce((select price_lists.price as customer_price
                         from price_lists
                         where product_id = products.id
                           and (`country_code` = ? OR `country_code` IS NULL)
                           AND (`currency_code` = ? OR `currency_code` IS NULL)
                           AND ((`start_date` <= ? AND `end_date` >= ?) OR
                                (`start_date` IS NULL AND `end_date` IS NULL))
                         order by CASE
                                      WHEN country_code = ? AND currency_code = ? AND start_date IS NOT NULL AND end_date IS NOT NULL THEN 1
                                      WHEN country_code = ? AND currency_code = ? AND start_date IS NULL AND end_date IS NULL THEN 2
                                      WHEN country_code = ? AND currency_code IS NULL AND start_date IS NOT NULL AND end_date IS NOT NULL THEN 3
                                      WHEN country_code = ? AND currency_code IS NULL AND start_date IS NULL AND end_date IS NULL THEN 4
                                      WHEN country_code IS NULL AND currency_code = ? AND start_date IS NOT NULL AND end_date IS NOT NULL THEN 5
                                      WHEN country_code IS NULL AND currency_code = ? AND start_date IS NULL AND end_date IS NULL THEN 6
                                      ELSE 7
                                   END,
                                  priority
                         limit 1), base_price) as applicable_price
                 ",
            [
                $countryCode,
                $currencyCode,
                $date,
                $date,
                $countryCode,
                $currencyCode,
                $countryCode,
                $currencyCode,
                $countryCode,
                $countryCode,
                $currencyCode,
                $currencyCode
            ]
        )->when(request('sort_col') == 'applicable_price', function (Builder $query) {
            $dir = request('sort_dir', 'asc');
            if (!in_array($dir, ['asc', 'desc', 'ASC', 'DESC'])) {
                $dir = 'asc';
            }
            $query->orderBy('applicable_price', $dir);
        });
    }
}
