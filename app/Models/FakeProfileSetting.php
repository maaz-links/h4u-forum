<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FakeProfileSetting extends Model
{
    //
    protected $fillable = [
        'script_name'
    ];
    
    public function dummy_users()
    {
        return $this->hasMany(User::class, 'dummy_id');
    }
    protected static function booted()
    {
        static::deleting(function ($parent) {
            //dd($parent,$parent->securefile);
            $parent->dummy_users->each(function ($child) {
                $child->delete();
            });
        });
    }
}
