<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'gender',
        'date_of_birth',
        'available_for',
        'available_services',
    ];
    protected $appends = ['available_services'];

    public function getAvailableServicesAttribute(){
        // dd($this->hostess_services->subscription);
        return $this->hostess_services->pluck('id');
        //return DB::table('hostess_service_pivot')->select('user_profile_id','hostess_service_id')->get();

    }

    // protected function available_for(): Attribute //productName == product_name
    // {
    //     return new Attribute(
    //         get: fn() => $this->fun1()
    //     );
    // }

    // protected function fun1(){
        
    // }

    // /**
    //  * Check if profile is available for specific service.
    //  *
    //  * @param  string  $service
    //  * @return bool
    //  */
    // public function isAvailableFor($service)
    // {
    //     return in_array($service, $this->available_for ?? []);
    // }
    public function hostess_services()
    {
        return $this->belongsToMany(HostessService::class,
        'hostess_service_pivot', 'user_profile_id', 'hostess_service_id')
        ->withTimestamps();
    }
}