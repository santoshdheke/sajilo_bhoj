<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('order')->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('method') && $request->method !== 'all') {
            $query->where('payment_method', $request->method);
        }

        $payments = $query->paginate(15)->withQueryString();

        $stats = [
            'collected_today' => Payment::where('status', 'completed')->whereDate('paid_at', today())->sum('amount'),
            'pending'         => Payment::where('status', 'pending')->count(),
            'completed'       => Payment::where('status', 'completed')->count(),
            'failed'          => Payment::where('status', 'failed')->count(),
        ];

        return view('admin.theme_one.payment.index', compact('payments', 'stats'));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'order_id'       => 'required|exists:orders,id',
            'payment_method' => 'required|string|max:30',
            'transaction_id' => 'nullable|string|max:150',
            'amount'         => 'required|numeric|min:0',
            'status'         => 'required|in:pending,completed,failed,refunded',
        ]);

        $data['paid_at'] = $data['status'] === 'completed' ? now() : null;

        $payment = Payment::create($data);
        $payment->load('order');

        return response()->json($payment);
    }

    public function update(Request $request, Payment $payment): JsonResponse
    {
        $data = $request->validate([
            'status' => 'required|in:pending,completed,failed,refunded',
        ]);

        $payment->update([
            'status'  => $data['status'],
            'paid_at' => $data['status'] === 'completed' ? ($payment->paid_at ?? now()) : $payment->paid_at,
        ]);

        return response()->json($payment->load('order'));
    }

    public function ordersWithoutPayment(): JsonResponse
    {
        $orders = Order::whereDoesntHave('payments', fn ($q) => $q->where('status', 'completed'))
            ->latest()
            ->limit(50)
            ->get(['id', 'order_no', 'customer_name', 'grand_total']);

        return response()->json($orders);
    }
}
