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

        $filters = [
            'payment_id' => fn($q, $value) => $q->where('payment_id', 'like', "%{$value}%"),
            'details' => fn($q, $value) => $q->where('details', 'like', "%{$value}%"),
            'email' => fn($q, $value) => $q->whereHas('project.user', fn($q) => $q->where('email', 'like', "%{$value}%")),
            'currency' => fn($q, $value) => $q->where('currency', '=', $value),
            'project_id' => fn($q, $value) => $q->where('project_id', '=', $value),
        ];

        foreach ($filters as $field => $filter) {
            if ($request->filled($field)) {
                $filter($query, $request->input($field));
            }
        }

        return $query->orderByDesc('id')->paginate(10);
    }
}
