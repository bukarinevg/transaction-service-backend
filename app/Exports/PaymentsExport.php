<?php

namespace App\Exports;


use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromView;

class PaymentsExport implements FromView
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $query = Payment::with('project', 'project.user');

        $query = Payment::applyFilters($query);

        $payments = $query->orderByDesc('id')->get();

        return view('exports.payments', compact('payments'));
    }
}
