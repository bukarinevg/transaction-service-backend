<?php

namespace App\Http\Controllers\api;

use App\Services\PaymentService;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Exports\PaymentsExport;
use Maatwebsite\Excel\Facades\Excel;

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
        $query = Payment::applyFilters($query);

        return $query->orderByDesc('id')->paginate(10);
    }

    public function export(Request $request)
    {
        return Excel::download(new PaymentsExport($request), 'payments.xlsx');
    }

    public function update(Request $request, Payment $payment, PaymentService $service)
    {
        try {
            $data = $request->validate([
                'details' => 'sometimes|string',
                'amount' => 'sometimes|numeric',
                'currency' => 'sometimes|string|size:3',
                'status' => 'sometimes|in:Оплачен',
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Validation failed', 'error' => $e->getMessage()], 400);
        }

        try {
            $payment = $service->updatePayment($payment, $data);
            return response()->json(['payment' => $payment], 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Update failed', 'error' => $e->getMessage()], 500);
        }
    }
}
