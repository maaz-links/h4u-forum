<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use Illuminate\Http\Request;

class AdminLogController extends Controller
{
    public function index(){
        $logs = AdminLog::latest()->get();
        //return $logs;
        return view("admin-logs.index",compact("logs"));
    }
}
