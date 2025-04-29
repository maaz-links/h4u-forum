<?php
// app/Models/Builders/UserQueryBuilder.php

namespace App\Models\Builders;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ChatQueryBuilder extends Builder
{
     public function myChats($userId){
        return $this->where(function($q) use ($userId) {
            $q->where('user1_id', $userId)
              ->orWhere('user2_id', $userId);
          });
     }
}