<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactRequest extends Model
{
    protected $fillable = ['name', 'email', 'message'];

    protected $casts = [
        'terms_accepted' => 'boolean',
        'created_at' => 'datetime',
    ];
}