<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShadowBan extends Model
{
    protected $fillable = ['user_id', 'expired_at'];
    protected $dates = ['expired_at'];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    // public function isPermanent(): bool
    // {
    //     return is_null($this->expired_at);
    // }
    
    // public function isTemporary(): bool
    // {
    //     return !$this->isPermanent();
    // }
    
    public function shadowBanActive(): bool
    {
        // return $this->isPermanent() || now()->lessThan($this->expired_at);
        return now()->lessThan($this->expired_at);
    }
}