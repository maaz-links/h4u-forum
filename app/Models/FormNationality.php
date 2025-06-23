<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormNationality extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'is_default'];

    public function isDefault(){
        return $this->is_default ? true : false;
    }

    /**
     * Scope a query to order by display order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }
}
