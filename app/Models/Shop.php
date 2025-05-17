<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
     protected $appends = ['icon_url'];

    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }


   public function getIconUrlAttribute()
{
    return $this->icon
        ? asset('shops/' . $this->icon)
        : null;
}
}
