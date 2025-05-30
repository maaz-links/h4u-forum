<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use App\Services\UserValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class UpdateProfileController2 extends Controller
{
    public function updateProfile(Request $request){
        $profile = UserProfile::where('user_id','=',$request->user()->id)->first();
      //option_available_for_ids
        $validated = $request->validate([
            'description' => 'required|string',
            'travel_available' => 'required|integer',
            'notification_pref' => 'required|integer',
            'visibility_status' => 'required|integer',

            'option_ids' => 'array',
            'option_ids.*' => 'integer|exists:interests,id',
            'option_available_for_ids' => 'array',
            'option_available_for_ids.*' => 'integer|exists:hostess_services,id',
            'option_language_ids' => 'array',
            'option_language_ids.*' => 'integer|exists:spoken_languages,id',

            'other_data' => 'array|required',
            'other_data.shoeSize' => 'required',
            'other_data.height' => 'required',
            'other_data.weight' => 'required',
            'other_data.eyeColor' => 'required',
            //'other_data.telegram' => 'required',
            'other_data.dressSize' => 'required',

            'nationality' => 'required|string',
            // 'province' => 'required|string',
            // 'country' => 'required|string',
            'selectedCountry' => 'integer|required|exists:europe_countries,id',
            'selectedProvince' => 'integer|required|exists:europe_provinces,id'

            //'other_options.*' => 'integer|exists:spoken_languages,id',
        
        ]);
        //return response()->json(['message' => $request->other_data['dressSize']], 200);
        $profile->shoe_size = $request->other_data['shoeSize'];
        $profile->height = $request->other_data['height'];
        $profile->eye_color = $request->other_data['eyeColor'];
        $profile->weight = $request->other_data['weight'];
        $profile->dress_size = $request->other_data['dressSize'];
        $profile->telegram = $request->other_data['telegram'];
        
        $profile->description= $request->description;

        $profile->travel_available= $request->travel_available;
        $profile->notification_pref= $request->notification_pref;
        $profile->visibility_status= $request->visibility_status;

        $profile->nationality= $request->nationality;
        // $profile->province= $request->province;
        // $profile->country= $request->country;
        $profile->country_id = $request->selectedCountry;
        $profile->province_id = $request->selectedProvince;

        $profile->save();
        $profile->interests()->sync($request->option_ids);
        $profile->hostess_services()->sync($request->option_available_for_ids);
        $profile->spoken_languages()->sync($request->option_language_ids);

        return response()->json(['message' => 'Submitted successfully'], 200);

    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            //'new_password' => 'required|min:8|confirmed',
        ]+UserValidation::rules(['password']),
        UserValidation::messages()
    );
    //dd('wat');

        if ($validator->fails()) {
            return response()->json(['formError' => $validator->errors()], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'formError' => [
                    'current_password' => ['The current password is incorrect.']
                ]
            ], 422);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.'
        ]);
    }

    public function deleteAccount(Request $request){
        $user = $request->user();

        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        $user->delete();
        
        return response()->json(['message'=>'User account successfully deleted']);
    }
}
