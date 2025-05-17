<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Services\PayPalService;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount * 100, 
                'currency' => 'usd',
                'payment_method_types' => ['card'],
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'paymentIntentId' => $paymentIntent->id, // <-- Send this to frontend

            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createOrder(Request $request)
    {
        $shop = Shop::findOrFail($request->shopId);
        $paypal = new PayPalService();

        $order = $paypal->createOrder($shop->price, $shop->id);

        $approvalUrl = collect($order['links'])->firstWhere('rel', 'approve')['href'];

        return response()->json([
            'approval_url' => $approvalUrl
        ]);
    }

    public function success(Request $request)
    {
        $paypal = new PayPalService();
        $order = $paypal->captureOrder($request->token); 

        if ($order['status'] === 'COMPLETED') {
            $user = Auth::user(); 
            $shopId = $order['purchase_units'][0]['custom_id'];

            $shop = Shop::findOrFail($shopId);
            $user->credits += $shop->credits;
            $user->save();

        return response()->json(['status' => true,'message'=>'Credits added in your account']);

        }
    }

}
