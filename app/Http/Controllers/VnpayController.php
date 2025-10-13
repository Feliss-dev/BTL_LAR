<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Services\VnpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class VnpayController extends Controller
{
    private $vnpayService;

    public function __construct(VnpayService $vnpayService) {
        $this->vnpayService = $vnpayService;
    }

    public function payment(Request $request) {
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

            // Populate VNPay parameters.
            $paymentParameters = $this->vnpayService->createPaymentParameters($total, $order->id, 'Thanh toan don hang ' . $order->order_number);
            $paymentParameters['vnp_IpAddr'] = $request->ip();

            // Sort the payment parameters ascending based on key.
            ksort($paymentParameters);

            // Concat all parameters into singular string, separated by ampersand.
            $hashData = implode('&', array_map(function ($key, $value) {
                return $key . '=' . urlencode($value);
            }, array_keys($paymentParameters), $paymentParameters));

            $secureHash = $this->vnpayService->computeSecureHash($hashData);

            $paymentParameters['vnp_SecureHash'] = $secureHash;

            $queryParameters = implode('&', array_map(function ($key, $value) {
                return $key . '=' . urlencode($value);
            }, array_keys($paymentParameters), $paymentParameters));

            return Redirect::to($this->vnpayService->endpoint() . '?' . $queryParameters);
        } catch (\Exception $e) {
            return redirect()->route('checkout')->with('error', 'Lỗi thanh toán VnPay: ' . $e->getMessage());
        }
    }

    public function return(Request $request) {
        try {
            $data = $request->all();

            $order = Order::find($data['vnp_TxnRef']);

            if ($data['vnp_ResponseCode'] == '00' || (int)$data['vnp_ResponseCode'] == 0) {
                // Thành công, cập nhật trạng thái đã trả tiền và cập nhật lại order_id cho giỏ hàng.
                $this->handleSuccessfulPayment($order);

                request()->session()->flash('success', 'Thanh toán thành công!');
                return redirect()->route('home')->with('succes');
            } else {
                $this->handleFailurePayment($order);

                request()->session()->flash('error', 'Thanh toán thất bại!');
                $this->handleFailurePayment($order);
            }
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Lỗi xử lý callback: ' . $e->getMessage());
        }
    }

//    public function ipn(Request $request) {
//        $data = $request->all();
//
//        $order = Order::find($data['vnp_TxnRef']);
//        if ($data['vnp_ResponseCode'] == '00' || (int)$data['vnp_ResponseCode'] == 0) {
//            // Thành công, cập nhật trạng thái đã trả tiền và cập nhật lại order_id cho giỏ hàng.
//            $this->handleSuccessfulPayment($order);
//        } else {
//            $this->handleFailurePayment($order);
//        }
//    }

    private function handleSuccessfulPayment(Order $order) {
        $order->update([
            'payment_status' => 'paid',
            'status' => 'process',
        ]);

        Cart::where('user_id', $order->user_id)->where('order_id', null)->update([
            'order_id' => $order->id,
        ]);
    }

    private function handleFailurePayment(Order $order) {
        // idk.
        Cart::where('order_id', $order->id)->update(['order_id' => null]);

        // Xóa order thất bại
        $order->delete();
    }
}
