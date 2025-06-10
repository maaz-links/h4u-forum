<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\ShopTransactionHistory;
use App\Models\UserProfile;
use Illuminate\Http\Request;

class ShopControllerOld extends Controller
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
    public function addCredits(Request $request)
    {
        $shopId = $request->shopId;

        $shop = Shop::where('id',$shopId)->first();
        $user = auth()->user();

        $userProfile = UserProfile::where('user_id',$user->id)->first();
        $userProfile->credits = $userProfile->credits + $shop->credits;
        $userProfile->save();

        $shops_transaction_history = new ShopTransactionHistory();

        $shops_transaction_history->user_id = $user->id;
        $shops_transaction_history->payment_id = $request->paymentIntentId ?? null;
        $shops_transaction_history->shop_id = $shop->id;
        $shops_transaction_history->rec_title = $shop->title;
        $shops_transaction_history->rec_price = $shop->price;
        $shops_transaction_history->rec_credits = $shop->credits;
        $shops_transaction_history->payment_method = $request->payment_method;
        $shops_transaction_history->save();

        return response()->json(['status' => true,'message'=>'Credits added in your account']);
    }

    public function userPurchased()
    {
        $user = auth()->user();

        $transactions = ShopTransactionHistory::with(['user','shop'])->where('user_id',$user->id)->orderBy('created_at','DESC')->get();

        return response()->json(['status' => true,'message'=>'Transactions fetched successfully','transactions'=>$transactions]);
    }

}
