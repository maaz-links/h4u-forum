<?php

// app/Models/Attachment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'path', 'is_profile_picture'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::deleting(function ($model) {
            if ($model->path) {
                if (Storage::disk('local')->exists($model->path)) {
                    Storage::disk('local')->delete($model->path);
                }
            }
        });
    }
}