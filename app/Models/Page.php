<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    //Use php artisan db:seed --class=PagesTableSeeder
    protected $fillable = ['slug', 'content'];
}