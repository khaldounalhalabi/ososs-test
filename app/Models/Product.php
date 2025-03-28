<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
