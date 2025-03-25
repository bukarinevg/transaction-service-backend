<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\User;
use App\Jobs\NotifyExternalService;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function createWithBalance(array $data): Payment
    {
        return DB::transaction(function () use ($data) {
            $payment = Payment::create($data);

            if ($payment->status === 'Оплачен') {
                $user = $payment->project->user;

                $balance = $user->balance()->firstOrCreate([
                    'user_id' => $user->id,
                ]);

                match (strtoupper($payment->currency)) {
                    'RUB' => $balance->increment('balance_rub', $payment->amount),
                    'USD' => $balance->increment('balance_usd', $payment->amount),
                    'KZT' => $balance->increment('balance_kzt', $payment->amount),
                };

                NotifyExternalService::dispatch($user);
            }

            return $payment;
        });
    }
}
