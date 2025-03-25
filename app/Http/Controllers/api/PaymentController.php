<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'payment_id' => 'required|uuid|unique:payments,payment_id',
            'project_id' => 'required|exists:projects,id',
            'details' => 'required|string',
            'amount' => 'required|numeric',
            'currency' => 'required|string|size:3',
            'status' => 'required|in:Оплачен,Не оплачен',
        ]);

        return Payment::create($data);
    }

    public function index()
    {
        return Payment::with('project')->get();
    }
    
}
