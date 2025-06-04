<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopTransactionHistory;
use App\Services\AuditAdmin;
use Illuminate\Http\Request;

class ShopTransactionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $transactions = ShopTransactionHistory::with(['user', 'shop'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('payment_id', 'like', "%{$search}%")
                    ->orWhere('rec_title', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
                    // ->orWhereHas('shop', function ($q3) use ($search) {
                    //     $q3->where('title', 'like', "%{$search}%");
                    // });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)->appends($request->except('page'));

        return view('shop-transactions.index', compact('transactions'));
    }


     public function destroy(Request $request)
    {
        $shop = ShopTransactionHistory::where('id',$request->id)->first();
        $shop->delete();

        AuditAdmin::audit("ShopTransactionController@destroy");


        return redirect()->route('shops.index')->with('success', 'Shop transaction deleted successfully.');
    }
}
