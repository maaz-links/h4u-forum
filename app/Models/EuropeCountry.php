<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EuropeCountry extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'europe_countries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'display_order',
        'is_default'
    ];

    /**
     * Get the provinces for the country.
     */
    public function provinces()
    {
        return $this->hasMany(EuropeProvince::class, 'country_id');
    }

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