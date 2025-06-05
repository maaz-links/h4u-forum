<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModerationRule extends Model
{
    protected $fillable = [
        'type',
        'name',
        'pattern',
        //'category',
        'severity',
        'is_active'
    ];

    public const TYPE_KEYWORD = 'keyword';
    public const TYPE_REGEX = 'regex';
}