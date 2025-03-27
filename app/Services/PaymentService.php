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

                $payment->applyBalance();

                NotifyExternalService::dispatch($user);
            }

            return $payment;
        });
    }

    public function updatePayment(Payment $payment, array $data): Payment
    {
        return DB::transaction(function () use ($payment, $data) {
            
            $statusChanged = isset($data['status']) && $data['status'] === 'Оплачен' && $payment->status !== 'Оплачен';
  
            if ($statusChanged) {
                $payment->status = 'Оплачен';
                $payment->applyBalance();
            }
        
            $payment->update($data);

            return $payment;
        });
    }
}
