<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserConfig;
use App\Services\AuditAdmin;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    public function index()
    {
        $configs = UserConfig::orderBy('key')->get();
        return view('credits.index', compact('configs'));
    }

    public function store(Request $request){

        $validated_data = $request->validate([
            'standard' => 'required|numeric|min:0|max:10000',
            'verified' => 'required|numeric|min:0|max:10000',
            'topprofile' => 'required|numeric|min:0|max:10000',
            'verified_topprofile' => 'required|numeric|min:0|max:10000',
        ],);

        // $validated2 = $request->validate([
        //     'admin_reason' => 'required|string',
        // ],);

        \DB::transaction(function () use ($validated_data,$request) {
            $results = [];
            
            foreach ($validated_data as $key => $value) {
                $results[$key] = UserConfig::updateOrCreate(
                    ['key' => "h4u.chatcost.{$key}"],
                    ['value' => $value]
                );
            }
            
            
            // AuditAdmin::audit("Modified Credits Cost",$request->admin_reason);
            AuditAdmin::audit("CreditController@store");
            return $results;
        });
        return redirect()->route('credits.index')->with('success', 'Credits modified successfully.');
    }
        
}
