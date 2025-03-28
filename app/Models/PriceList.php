<?php

namespace App\Models;

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
}
