<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NotifyExternalService implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle(): void
    {
        $balance = $this->user->balance;

        if (!$balance) return;

        $payload = [
            'email' => $this->user->email,
            'balance_rub' => $balance->balance_rub,
            'balance_usd' => $balance->balance_usd,
            'balance_kzt' => $balance->balance_kzt,
        ];

        Http::post(config('services.external_webhook.url'), $payload);
    }

    public function backoff(): array
    {
        return [10, 30, 60, 300]; 
    }

    public function retryUntil()
    {
        return now()->addMinutes(10); 
    }
}
