<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MomoService
{
    protected $endpoint;
    protected $partnerCode;
    protected $accessKey;
    protected $secretKey;
    protected $redirectUrl;
    protected $ipnUrl;
    protected $requestType = 'payWithMethod';

    public function __construct()
    {
        $this->endpoint = config('payment.momo.endpoint');
        $this->partnerCode = config('payment.momo.partner_code');
        $this->accessKey = config('payment.momo.access_key');
        $this->secretKey = config('payment.momo.secret_key');
        $this->redirectUrl = route('momo.callback');
        $this->ipnUrl = route('momo.ipn');
    }

    public function createPayment($amount, $orderId, $orderInfo = 'Thanh toán hóa đơn')
    {
        try {
            $requestId = $this->partnerCode . time();
            $extraData = '';

            $rawSignature = "accessKey={$this->accessKey}&amount={$amount}&extraData={$extraData}&ipnUrl={$this->ipnUrl}&orderId={$orderId}&orderInfo={$orderInfo}&partnerCode={$this->partnerCode}&redirectUrl={$this->redirectUrl}&requestId={$requestId}&requestType={$this->requestType}";
            $signature = hash_hmac('sha256', $rawSignature, $this->secretKey);

            $data = [
                'partnerCode' => $this->partnerCode,
                'accessKey' => $this->accessKey,
                'requestId' => $requestId,
                'amount' => $amount,
                'orderId' => $orderId,
                'orderInfo' => $orderInfo,
                'redirectUrl' => $this->redirectUrl,
                'ipnUrl' => $this->ipnUrl,
                'extraData' => $extraData,
                'requestType' => $this->requestType,
                'signature' => $signature,
                'lang' => 'vi'
            ];

            $response = Http::timeout(30)->post($this->endpoint, $data);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('MoMo API Error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('MoMo Payment Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function verifySignature($data)
    {
        $rawSignature = "accessKey={$this->accessKey}&amount={$data['amount']}&extraData={$data['extraData']}&message={$data['message']}&orderId={$data['orderId']}&orderInfo={$data['orderInfo']}&orderType={$data['orderType']}&partnerCode={$this->partnerCode}&payType={$data['payType']}&requestId={$data['requestId']}&responseTime={$data['responseTime']}&resultCode={$data['resultCode']}&transId={$data['transId']}";
        $signature = hash_hmac('sha256', $rawSignature, $this->secretKey);

        return $signature === $data['signature'];
    }
}
