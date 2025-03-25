<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request)
    {

        try {
            $data =  $request->validate([
                'project_id' => 'required|exists:projects,id',
                'details' => 'required|string',
                'amount' => 'required|numeric',
                'currency' => 'required|string|size:3',
                'status' => 'required|in:Оплачен,Не оплачен',
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Validation failed', 'error' => $e->getMessage()], 400);
        }

        $data['payment_id'] = (string) \Illuminate\Support\Str::uuid();
        $payment = Payment::create($data);

        return response()->json(['payment' => $payment], 201);
    }

    public function index()
    {
        return Payment::with('project')->get();
    }
    
}
