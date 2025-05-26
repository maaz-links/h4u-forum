<?php

namespace App\Http\Controllers\Admin;

use App\Events\PermaBan;
use App\Events\TempBan;
use App\Events\UnBanUser;
use App\Events\WarnUser;
use App\Http\Controllers\Controller;
use App\Models\Ban;
use App\Models\User;
use App\Services\AuditAdmin;
use Illuminate\Http\Request;

class BanController extends Controller
{
    public function showBanManagement($username)
    {
        $user = User::where("name", $username)->first();
        if(!$user){
            abort(404);
        }
        //$user->load("bans");
        $user->load(['bans' => function($query) {
            $query->whereNull('expired_at') // Permanent bans
                  ->orWhere('expired_at', '>', now()); // Active temporary bans
        }]);
        return view('user-profile.ban', compact('user'));
    }

    public function ban(User $user, Request $request)
    {
        // $request->validate([
        //     'reason' => 'required|string|max:255'
        //     ]
        // );
        //dd($user->id);
        //$user->ban(['reason' => $request->reason]);
        $user->unban();
        Ban::create([
            'user_id' => $user->id,
            //'reason'=> $request->reason
        ]);
        AuditAdmin::audit("BanController@ban");
        event(new PermaBan($user));
        
        return redirect()->route('admin.users.ban.show', $user->name)
            ->with('success', 'User has been permanently banned.');
    }

    public function tempBan(User $user, Request $request)
    {
        $request->validate([
            //'reason' => 'required|string|max:255',
            'days' => 'required|integer|min:1'
        ]);
        
        // $user->ban([
        //     'expired_at' => now()->addDays($request->days),
        //     'reason' => $request->reason
        // ]);
        $user->unban();
        $ban = Ban::create([
            'user_id' => $user->id,
            //'reason'=> $request->reason,
            'expired_at' => now()->addDays((int) $request->days),
        ]);
        AuditAdmin::audit("BanController@tempban");
        event(new TempBan($user,$ban->expired_at));
        
        return redirect()->route('admin.users.ban.show', $user->name)
            ->with('success', "User has been temporarily banned for {$request->days} days.");
    }

    public function unban(User $user)
    {
        $user->unban();
        AuditAdmin::audit("BanController@unban");
        event(new UnBanUser($user));
        return redirect()->route('admin.users.ban.show', $user->name)
            ->with('success', 'User ban has been lifted.');
    }

    public function warn(User $user)
    {
        $user->profile()->increment('warnings');
        AuditAdmin::audit("BanController@warning");
        event(new WarnUser($user));
        return redirect()->route('admin.users.ban.show', $user->name)
            ->with('success', 'User has been warned.');
    }
}
