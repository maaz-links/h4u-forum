<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\ShopTransactionHistory;
use App\Models\UserProfile;
use App\Services\PayPalService;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Log;
use Stripe\Stripe;

class ShopController extends Controller
{
    public function index()
    {
        $shops = Shop::orderBy('created_at','DESC')->get();

        return response()->json(['status'=>true,'message'=>'Shops fetched successfully','shops'=>$shops]);
    }

    public function shop($id)
    {
       $shop = Shop::find($id);

       return response()->json(['status'=>true,'message'=>'Shop fetched','shop' =>$shop]);
    }
    // public function addCredits(Request $request)
    // {
    //     $validated = $request->validate([
    //         'paymentIntentId' => 'required|string',
    //         'shopId' => 'required|numeric|exists:shops,id',
    //     ]);
        
    //     Stripe::setApiKey(config('services.stripe.secret'));
    //     $paymentIntent = \Stripe\PaymentIntent::retrieve($validated['payment_intent_id']);
        
    //     if ($paymentIntent->status !== 'succeeded') {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Payment not completed'
    //         ], 400);
    //     }
    //     $shopId = $request->shopId;

    //     $shop = Shop::where('id',$shopId)->first();
    //     $user = auth()->user();

    //     $userProfile = UserProfile::where('user_id',$user->id)->first();
    //     $userProfile->credits = $userProfile->credits + $shop->credits;
    //     $userProfile->save();

    //     $shops_transaction_history = new ShopTransactionHistory();

    //     $shops_transaction_history->user_id = $user->id;
    //     $shops_transaction_history->payment_id = $request->paymentIntentId ?? null;
    //     $shops_transaction_history->shop_id = $shop->id;
    //     $shops_transaction_history->payment_method = $request->payment_method;
    //     $shops_transaction_history->save();

    //     return response()->json(['status' => true,'message'=>'Credits added in your account']);
    // }

    public function addCredits(Request $request)
{
    try {
        // Validate request data
        $validated = $request->validate([
            'paymentIntentId' => 'required|string',
            'shopId' => 'required|numeric|exists:shops,id',
            'payment_method' => 'required|string|in:paypal,stripe',
            'token' => 'nullable|string',
        ]);

        // Get shop and user data
        $shop = Shop::findOrFail($validated['shopId']);
        $user = auth()->user();

        
        if( ($request->payment_method == 'paypal') && $validated['paymentIntentId']) {
            //REMEMBER, ADD TOKEN IN TRANSACTION HISTORY, NOT PAYERID indicated by PaymentIntentID

            $paypal = new PayPalService();
            // Verify the payment with PayPal API
            $paymentDetails = $paypal->captureOrder($validated['paymentIntentId']);
            //return $paymentDetails;
            // Check if payment was successful and amount matches
            // dd($paymentDetails);
            if ($paymentDetails['status'] !== 'COMPLETED' 
            // || 
            //     $paymentDetails['purchase_units'][0]['payments']['captures'][0]['amount']['value'] != $shop->price
                ) {
                return response()->json([
                    'status' => false,
                    'message' => 'Payment not completed or verification failed'
                ], 400);
            }
        }else {
            // Initialize Stripe
            Stripe::setApiKey(config('services.stripe.secret'));

            // Verify payment intent
            $paymentIntent = \Stripe\PaymentIntent::retrieve($validated['paymentIntentId']);
            
            // Check payment status
            if ($paymentIntent->status !== 'succeeded') {
                return response()->json([
                    'status' => false,
                    'message' => 'Payment not completed or verification failed'
                ], 400);
            }
        }
        // Check if payment was already processed
        if (ShopTransactionHistory::where('payment_id', $validated['paymentIntentId'])->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'This payment has already been processed'
            ], 400);
        }

        
        $userProfile = UserProfile::where('user_id', $user->id)->firstOrFail();

        // Start database transaction
        DB::beginTransaction();

        try {
            // Update user credits
            $userProfile->credits += $shop->credits;
            $userProfile->save();

            // Record transaction
            $shops_transaction_history = new ShopTransactionHistory();
            $shops_transaction_history->user_id = $user->id;
            $shops_transaction_history->payment_id = $validated['paymentIntentId'];
            $shops_transaction_history->shop_id = $shop->id;
            $shops_transaction_history->rec_title = $shop->title;
            $shops_transaction_history->rec_price = $shop->price;
            $shops_transaction_history->rec_credits = $shop->credits;
            $shops_transaction_history->payment_method = $validated['payment_method'];
            $shops_transaction_history->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Credits added to your account',
                'credits' => $userProfile->credits
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Credit processing failed: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'payment_intent' => $validated['paymentIntentId']
            ]);
            
            return response()->json([
                'status' => false,
                'message' => 'Failed to process credits. Please contact support.'
            ], 500);
        }

    } catch (\Stripe\Exception\ApiErrorException $e) {
        return response()->json([
            'status' => false,
            'message' => 'Payment verification failed: ' . $e->getMessage()
        ], 400);

    } catch (ModelNotFoundException $e) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid shop or user profile'
        ], 404);

    } catch (ValidationException $e) {
        return response()->json([
            'status' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        Log::error('Unexpected error in addCredits: ' . $e->getMessage(), [
            'exception' => $e,
            'request' => $request->all()
        ]);
        
        throw $e;
        // return response()->json([
        //     'status' => false,
        //     'message' => 'An unexpected error occurred. Please try again later.'
        // ], 500);
    }
}

    public function userPurchased()
    {
        $user = auth()->user();

        $transactions = ShopTransactionHistory::with(['user','shop'])->where('user_id',$user->id)->orderBy('created_at','DESC')->get();

        return response()->json(['status' => true,'message'=>'Transactions fetched successfully','transactions'=>$transactions]);
    }

}
