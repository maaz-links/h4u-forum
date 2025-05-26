<?php

namespace App\Http\Controllers\Admin;

use App\Events\GenerateFakeProfiles;
use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\EuropeCountry;
use App\Models\EuropeProvince;
use App\Models\FakeProfileSetting;
use App\Models\HostessService;
use App\Models\Interest;
use App\Models\SpokenLanguage;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\AuditAdmin;
use Faker\Factory as Faker;
use File;
use Hash;
use Illuminate\Http\Request;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use Propaganistas\LaravelPhone\PhoneNumber;
use Storage;
use Str;

class FakeProfileSettingController extends Controller
{
    public function index(){
        $scripts = FakeProfileSetting::all();
        return view("profile-scripts.index",compact('scripts'));
    }
    public function create(){
        $provinces = EuropeProvince::orderBy("name")->get();
        //return $provinces;
        return view("profile-scripts.create",compact('provinces'));
    }
    public function destroy(Request $request){
        //dd($request);
        $validated_data = $request->validate([
            'script_id' => 'required|numeric|exists:fake_profile_settings,id',
            // 'admin_reason' => 'required|string',
        ]);

        $script = FakeProfileSetting::where('id', $validated_data['script_id'])
        ->first();

        //return $review;
        // AuditAdmin::audit(
        //     "Deleted script ID {$validated_data['script_id']}",
        //     $request->admin_reason
        // );
        $script->delete();

        AuditAdmin::audit("FakeProfileSettingController@destroy");

        return back()->with("success","All users associated with the script are deleted");
    }
    public function store(Request $request){

        $validatedData = $request->validate([
            //'name' => 'required|string',
            'script_name' => 'required|string',
            'profile_count' => 'required|integer|min:1|max:50',
            'min_age' => 'required|integer|min:18',
            'max_age' => 'required|integer|gt:min_age|max:100',
            //'gender' => 'required|string',
            // 'province' => 'integer|required|exists:europe_provinces,id'
            // 'locations' => 'required|array',
            // 'interests' => 'required|array',
            // 'image_directory' => 'nullable|string',
            // 'random_online_status' => 'boolean',
            // 'activity_frequency' => 'integer|min:1|max:20',
        ],[
            //'province.integer' => 'The province field is required.'
        ]);

        
        //dd($validatedData);
        $setting = FakeProfileSetting::create([
            'script_name' => $request->script_name,
        ]);

        AuditAdmin::audit("FakeProfileSettingController@store");

        event(new GenerateFakeProfiles($validatedData, $setting));

        return redirect()->route('profile-scripts.index')->with('success',"Script created and Profiles Generated");
    }

}
