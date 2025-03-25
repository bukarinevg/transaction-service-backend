<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'payment_id',
        'project_id',
        'details',
        'amount',
        'currency',
        'status',
    ];

    protected static function booted()
    {
        static::created(function ($payment) { 
            if ($payment->status === 'Оплачен') {
                $user = $payment->project->user;

                $balance = $user->balance()->firstOrCreate([
                    'user_id' => $user->id,
                ]);


                switch (strtoupper($payment->currency)) {
                    case 'RUB':
                        $balance->increment('balance_rub', $payment->amount);
                        break;
                    case 'USD':
                        $balance->increment('balance_usd', $payment->amount);
                        break;
                    case 'KZT':
                        $balance->increment('balance_kzt', $payment->amount);
                        break;
                }
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
