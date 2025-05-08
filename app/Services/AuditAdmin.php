<?php

namespace App\Services;

use App\Models\AdminLog;
use Auth;
use Illuminate\Http\Request;

class AuditAdmin
{
    public static function audit($action,$reason){
        $admin = Auth::user();
        //dd($admin);
            AdminLog::create([
                'admin_id' => $admin->id,
                'admin_name' => $admin->name,
                'action' => $action,
                'reason' => $reason,

            ]);
    }

}