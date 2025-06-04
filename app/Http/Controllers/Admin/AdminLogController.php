<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Validator;

class AdminLogController extends Controller
{
    public function index(Request $request){
        //$logs = AdminLog::latest()->get();
        //return $logs;
        $query = AdminLog::orderBy('created_at', 'desc');

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where('admin_name', 'like', "%{$searchTerm}%")
                  ->orWhere('action', 'like', "%{$searchTerm}%");
        }

        $logs = $query->paginate(10)->appends($request->except('page'));
        return view("admin-logs.index",compact("logs"));
    }

    public function showChangePasswordForm()
    {
        return view('change-password.password');
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $validator->after(function ($validator) use ($request) {
            if (!Hash::check($request->current_password, auth()->user()->password)) {
                $validator->errors()->add('current_password', 'Your current password is incorrect.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update password
        auth()->user()->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('password.change')
            ->with('success', 'Password changed successfully!');
    }
}
