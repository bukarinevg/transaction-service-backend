<?php

namespace App\Models;

use App\Jobs\NotifyExternalService;
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

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public static function applyBalance($payment) : void
    {
        $user = $payment->project->user;

        $balance = $user->balance()->firstOrCreate([
            'user_id' => $user->id,
        ]);

        match ($payment->currency) {
            'RUB' => $balance->increment('balance_rub', $payment->amount),
            'USD' => $balance->increment('balance_usd', $payment->amount),
            'KZT' => $balance->increment('balance_kzt', $payment->amount),
            default => null,
        };

        NotifyExternalService::dispatch($user);
    }
}
