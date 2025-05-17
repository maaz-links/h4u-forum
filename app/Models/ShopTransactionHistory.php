<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopTransactionHistory extends Model
{
    protected $table ="shops_transaction_history";

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');

    }

      public function shop()
    {
        return $this->belongsTo(Shop::class,'shop_id','id');

    }
}
