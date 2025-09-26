<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class PaymentController extends Controller
{
    public function failed(Request $request)
    {
        $orderId = $request->get('orderId');
        $order = Order::where('order_number', $orderId)->first();

        return view('frontend.pages.payment-failed', compact('order', 'orderId'));
    }

    public function retry($orderId)
    {
        $order = Order::where('order_number', $orderId)->first();

        if (!$order) {
            return redirect()->route('home')->with('error', 'Đơn hàng không tồn tại');
        }

        return view('frontend.pages.payment-retry', compact('order'));
    }
}
