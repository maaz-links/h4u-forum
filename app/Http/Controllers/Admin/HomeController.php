<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Chat;
use App\Models\Review;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\AuditAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    // public function index()
    // {
    //     $users = User::with(['profile' => function ($query) {
    //         $query->select('user_id','top_profile','verified_profile','credits'); // select only needed columns
    //     }])
    //     ->select('id', 'name', 'role', 'created_at')
    //     ->whereIn('role', [User::ROLE_KING, User::ROLE_HOSTESS])
    //     ->get();

    //     return view('home',compact('users'));
    // }
    public function index(Request $request)
    {
        $query = User::with(['profile' => function($query) {
                $query->select('user_id', 'top_profile', 'verified_profile', 'credits');
            }])
            ->select('id', 'name', 'role', 'created_at','dummy_id')
            ->forRoleAny()
            ->latest();

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                //$q->where('id', 'like', "%{$searchTerm}%")
                $q->where('name', 'like', "%{$searchTerm}%")
                //->orWhere('role', 'like', "%{$searchTerm}%")
                // ->orWhereHas('profile', function($profileQuery) use ($searchTerm) {
                //     $profileQuery->where('top_profile', 'like', "%{$searchTerm}%")
                //                 ->orWhere('verified_profile', 'like', "%{$searchTerm}%");
                //                 // Note: Searching numeric 'credits' field as string for simplicity
                //                 // For exact number matching, use where('credits', $searchTerm)
                // })
                ;
            });
        }

        // // Optional role filter
        // if ($request->filled('role')) {
        //     $query->where('role', $request->role);
        // }

        // // Optional profile status filters
        // if ($request->filled('top_profile')) {
        //     $query->whereHas('profile', function($q) use ($request) {
        //         $q->where('top_profile', $request->top_profile);
        //     });
        // }
        
        // if ($request->filled('verified_profile')) {
        //     $query->whereHas('profile', function($q) use ($request) {
        //         $q->where('verified_profile', $request->verified_profile);
        //     });
        // }

        $users = $query->paginate(10)
            ->appends($request->except('page'));

        return view('home', compact('users'));
    }

    public function profile(string $name){
        $user = User::with(['profile'=> function ($query) {
        }])->forUsername($name)
        ->forRoleAny()
        ->first();
        if(!$user){
            abort(404);
        }
        $user->rating = $user->getRatingAttribute();
        $user = new UserResource($user);
        return view('user-profile.index',compact('user'));
    }

    public function toggleVerified(User $user)
    {
        $prof = UserProfile::where('user_id',$user->id)->first();
        // dd($user,$prof);
        $msg = $prof->verified_profile ? 'This user is no longer Verified' : 'This user is now Verified';
        $prof->update([
            'verified_profile' => !$prof->verified_profile
        ]);

        AuditAdmin::audit("HomeController@toggleVerified");

        return redirect()->route('user-profile', $user->name)
            ->with('success', $msg);
    }

    public function toggleTop(User $user)
    {
        $prof = UserProfile::where('user_id',$user->id)->first();
        // dd($user,$prof);
        $msg = $prof->top_profile ? 'This user is no longer Top Profile' : 'This user is now Top Profile';
        $prof->update([
            'top_profile' => !$prof->top_profile
        ]);
        AuditAdmin::audit("HomeController@toggleTop");
        return redirect()->route('user-profile', $user->name)
            ->with('success', $msg);
    }

    public function editUserPassword($id)
    {
        $user = User::where('id','=', $id)
        ->forRoleAny()
        ->first();
        if(!$user){
            abort(404);
        }
        return view('user-profile.user-password',compact('user'));
    }

    public function updateUserPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            //'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        
        //dd($validator);
        $user = User::where('id','=', $request->user_id)
        ->forRoleAny()
        ->first();
        if(!$user){
            abort(404);
        }

        // $validator->after(function ($validator) use ($request) {
        //     if (!Hash::check($request->current_password, auth()->user()->password)) {
        //         $validator->errors()->add('current_password', 'Your current password is incorrect.');
        //     }
        // });
        //dd($validator);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        
        // Update password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        AuditAdmin::audit("HomeController@updateUserPassword");


        return redirect()->route('user-profile.password.edit', $user->id)
            ->with('success', "$user->name's Password changed successfully!");
    }

    public function loginAsUser(string $name)
    {
        $user = User::where('name',$name)->forRoleAny()->first();
        if(!$user){
            abort(404);
        }
        if($user->activeBan()){
            return redirect()->back()
            ->with('error', "Cannot Log In as Banned User");
        }
        $signedUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'admin.impersonation',
            now()->addMinutes(1),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );
        // AuditAdmin::audit("HomeController@loginAsUser");
        // // dd($signedUrl,parse_url($signedUrl, PHP_URL_QUERY));
        // // Extract just the path and query string to pass to frontend
        // $pathWithQuery = parse_url($signedUrl, PHP_URL_PATH) . '?' . parse_url($signedUrl, PHP_URL_QUERY);
        // //dd($pathWithQuery,urlencode($pathWithQuery));
        // return redirect(env('FRONTEND_URL'). '/secret-login?verify=' . urlencode($pathWithQuery));


        // Parse the signed URL to extract parameters
        $parsedUrl = parse_url($signedUrl);
        parse_str($parsedUrl['query'], $queryParams);

        AuditAdmin::audit("HomeController@loginAsUser");

        // Redirect with separate parameters
        return redirect(env('FRONTEND_URL') . '/secret-login?' . http_build_query([
            'id' => $user->id,
            'hash' => sha1($user->email),
            'expires' => $queryParams['expires'],
            'signature' => $queryParams['signature']
        ]));

    }


    
}
