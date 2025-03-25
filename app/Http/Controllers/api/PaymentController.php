<?php

namespace App\Http\Controllers\api;

use App\Services\PaymentService;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function store(Request $request, PaymentService $service)
    {
        try {
            $data = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'details' => 'required|string',
                'amount' => 'required|numeric',
                'currency' => 'required|string|size:3',
                'status' => 'required|in:Оплачен,Не оплачен',
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Validation failed', 'error' => $e->getMessage()], 400);
        }

        $data['payment_id'] = (string) Str::uuid();

        try {
            $payment = $service->createWithBalance($data);
            return response()->json(['payment' => $payment], 201);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Payment failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        $query = Payment::with('project', 'project.user');

        // Поиск по payment_id
        if ($request->filled('payment_id')) {
            $query->where('payment_id', 'like' , "%{$request->payment_id}%");   
        }
    
        // Поиск по реквизитам
        if ($request->filled('details')) {
            $query->where('details', 'like', "%{$request->details}%");
        }
    
        // Поиск по логину пользователя через project → user
        if ($request->filled('email')) {
            $query->whereHas('project.user', function ($q) use ($request) {
                $q->where('email', 'like', "%{$request->email}%");
            });
        }
    
        // Фильтр по валюте
        if ($request->filled('currency')) {
            $query->where('currency', '=', $request->currency);
        }
    
        // Фильтр по проекту
        if ($request->filled('project_id')) {
            $query->where('project_id', '=', $request->project_id);
        }
    
        return $query->orderByDesc('created_at')->paginate(10);
    }
}
