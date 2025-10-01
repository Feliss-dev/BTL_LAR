<?php

namespace App\Services;

use Carbon\Carbon;

class VnpayService {
    private $returnUrl;
    private $endpoint;
    private $tmnCode;
    private $version;
    private $secretKey;

    public function __construct() {
        $this->returnUrl = route('vnpay.return');
        $this->tmnCode = config('payment.vnpay.tmn_code');
        $this->version = config('payment.vnpay.version');
        $this->endpoint = config('payment.vnpay.endpoint');
        $this->secretKey = config('payment.vnpay.secret_key');
    }

    public function createPaymentParameters($amount, $orderId, $orderInfo = 'Thanh toan hoa don') : array {
        $now = Carbon::now();

        $parameters = [
            'vnp_Amount' => $amount * 100,
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => $now->format('YmdHis'),
            'vnp_ExpireDate' => $now->addMinutes(30)->format('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_Locale' => 'vn',
            'vnp_OrderInfo' => $orderInfo,
            'vnp_OrderType' => 'billpayment',
            'vnp_ReturnUrl' => $this->returnUrl,
            'vnp_TmnCode' => $this->tmnCode,
            'vnp_TxnRef' => $orderId,
            'vnp_Version' => $this->version,
        ];

        return $parameters;
    }

    public function computeSecureHash(string $parameters) : string {
        return hash_hmac('sha512', $parameters, $this->secretKey);
    }

    public function endpoint() {
        return $this->endpoint;
    }
}
