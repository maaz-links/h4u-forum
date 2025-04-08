<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function index(){
        $profile = UserProfile::get();
        return response()->json($profile);
    }
}
