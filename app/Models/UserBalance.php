<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBalance extends Model
{
    protected $fillable = ['user_id', 'balance_rub', 'balance_usd', 'balance_kzt'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
