<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Http\Helper;
use App\Services\MomoService;

class MomoController extends Controller
{
    protected $momoService;

    public function __construct(MomoService $momoService)
    {
        $this->momoService = $momoService;
    }

    public function payment()
    {
        try {
            $cart = Cart::where('user_id', auth()->user()->id)
                ->where('order_id', null)
                ->get();

            if ($cart->isEmpty()) {
                return redirect()->route('cart')->with('error', 'Giỏ hàng trống');
            }

            // Lấy order_id từ session (được tạo trong OrderController)
            $orderId = session()->get('id');
            if (!$orderId) {
                return redirect()->route('checkout')->with('error', 'Không tìm thấy thông tin đơn hàng');
            }

            $order = Order::find($orderId);
            if (!$order) {
                return redirect()->route('checkout')->with('error', 'Đơn hàng không tồn tại');
            }

            $total = $order->total_amount;

            $response = $this->momoService->createPayment(
                $total,
                $order->order_number,
                'Thanh toán đơn hàng #' . $order->order_number
            );

            if ($response['resultCode'] == 0) {
                return redirect($response['payUrl']);
            }

            return redirect()->route('checkout')->with('error', 'Không thể tạo thanh toán MoMo: ' . $response['message']);
        } catch (\Exception $e) {
            return redirect()->route('checkout')->with('error', 'Lỗi thanh toán MoMo: ' . $e->getMessage());
        }
    }

    public function callback(Request $request)
    {
        try {
            $data = $request->all();

            // Verify signature
            if (!$this->momoService->verifySignature($data)) {
                return redirect()->route('home')->with('error', 'Chữ ký không hợp lệ');
            }

            if ($data['resultCode'] == 0) {
                // Thanh toán thành công
                $this->updateOrderStatus($data['orderId'], 'paid');

                // Xóa session cart và coupon như PayPal
                session()->forget('cart');
                session()->forget('coupon');

                request()->session()->flash('success', 'You successfully pay from MoMo! Thank You');
                return redirect()->route('home');
            } else {
                // Thanh toán thất bại - xóa order và reset cart
                $this->handleFailedPayment($data['orderId']);
                request()->session()->flash('error', 'Thanh toán thất bại: ' . $data['message']);
                return redirect()->route('home');
            }
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Lỗi xử lý callback: ' . $e->getMessage());
        }
    }

    public function ipn(Request $request)
    {
        // Xử lý IPN từ MoMo
        $data = $request->all();

        if ($this->momoService->verifySignature($data)) {
            if ($data['resultCode'] == 0) {
                $this->updateOrderStatus($data['orderId'], 'paid');
            } else {
                $this->handleFailedPayment($data['orderId']);
            }
        }

        return response()->json(['status' => 'success']);
    }

    protected function updateOrderStatus($orderNumber, $status)
    {
        $order = Order::where('order_number', $orderNumber)->first();
        if ($order) {
            $order->update([
                'payment_status' => $status,
                'status' => $status === 'paid' ? 'process' : 'new'
            ]);

            // Nếu thanh toán thành công và chưa cập nhật cart
            if ($status === 'paid') {
                Cart::where('user_id', $order->user_id)
                    ->where('order_id', null)
                    ->update(['order_id' => $order->id]);
            }
        }
    }

    protected function handleFailedPayment($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->first();
        if ($order) {
            // Reset cart items về trạng thái chưa có order
            Cart::where('order_id', $order->id)
                ->update(['order_id' => null]);

            // Xóa order thất bại
            $order->delete();
        }
    }
}
