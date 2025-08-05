<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class PayPalService
{
    protected $clientId;
    protected $secret;
    protected $baseUrl;

    public function __construct()
    {
        $this->clientId = config('services.paypal.client_id');
        $this->secret = config('services.paypal.secret');
        $this->baseUrl = filter_var(config('services.paypal.sandbox'), FILTER_VALIDATE_BOOLEAN)
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }

    private function getAccessToken()
    {
        $response = Http::withBasicAuth($this->clientId, $this->secret)
            ->asForm()
            ->post("{$this->baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        return $response->json()['access_token'];
    }

    public function createOrder($amount, $shopId)
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)->post("{$this->baseUrl}/v2/checkout/orders", [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount' => [
                    'currency_code' => 'EUR',
                    'value' => $amount,
                ],
                'custom_id' => $shopId,
            ]],
            'application_context' => [
            'return_url' => env('FRONTEND_URL') . '/paypal/success?shopId=' . $shopId, // Include shopId in the return_url
            'cancel_url' => env('FRONTEND_URL').'/paypal/cancel',
            ],
        ]);

        return $response->json();
    }

    public function captureOrder($orderId)
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->post("{$this->baseUrl}/v2/checkout/orders/{$orderId}/capture",[
                'intent' => 'CAPTURE',
            ]);

        return $response->json();
    }
}
