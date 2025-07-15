<?php

namespace App\Http\Controllers;

use App\Events\CancellationRequest;
use App\Models\UserProfile;
use App\Services\ProfileValidation;
use App\Services\UserValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use Propaganistas\LaravelPhone\PhoneNumber;
class UpdateProfileController extends Controller
{
    public function updateProfile(Request $request){
        $profile = UserProfile::where('user_id','=',$request->user()->id)->first();
      //option_available_for_ids
        $validator = Validator::make(
            $request->all(),
            ProfileValidation::rules(),
            ProfileValidation::messages()
        );

        if ($validator->fails()) {
            return response()->json(['formError' => $validator->errors()], 422);
        }

        // if(\App\Models\FormNationality::where('name',$request->nationality)->first() == false){
        //     return response()->json(['formError' => ['Nationality is invalid']], 422);
        // }

        // if(\App\Models\FormEyeColor::where('name',$request->other_data['eyeColor'])->first() == false){
        //     return response()->json(['formError' => ['Eye Color is invalid']], 422);
        // }
        
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
            'current_password' => 'required|string',
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
    public function sendCancellationRequest(Request $request){
        $user = $request->user();

        event(new CancellationRequest($user));
        
        return response()->json(['message'=>'Cancellation Request Sent']);
    }

    public function updatePersonalInfo(Request $request){
    
        $user = $request->user();

        $changeName = [];
        //dd($user->name,$request->name);
        if($user->name !== $request->name){
            $changeName = ['name'];
        }
        $validator = Validator::make(
            $request->all(),
            UserValidation::rules(array_merge(['dob', 'phone'], $changeName)),
            UserValidation::messages()
        );

        //dd($validator);
        if ($validator->fails()) {
            return response()->json(['formError' => $validator->errors()], 422);
        }

        $phoneNumber = new PhoneNumber($request->phone);
        $phoneNumber = $phoneNumber->formatE164();

        $user->update([
            'name' => $request->name,
            'dob' => $request->dob,
            'phone'=> $phoneNumber,
        ]);

        return response()->json(['message' => 'Submitted successfully'], 200);
    }
}
