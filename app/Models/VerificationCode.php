<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer id
 * @property integer user_id
 * @property string  code
 * @property Carbon  valid_until
 * @property boolean is_valid
 */
class VerificationCode extends Model
{
    protected $guarded = ['id'];
    protected $fillable = [
        'user_id', 'code', 'valid_until', 'is_valid'
    ];

    protected function casts(): array
    {
        return [
            'valid_until' => 'datetime'
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
