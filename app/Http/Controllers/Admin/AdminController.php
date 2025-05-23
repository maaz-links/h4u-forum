<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminController extends Controller
{
    public function index()
    {
        $admins = User::where('role',"ADMIN")->with('roles')->get();

        return view('admin.users.index', compact('admins'));
    }

    public function create()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('admin.users.create', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            //'role' => 'required|exists:roles,id',
            // 'permissions' => 'nullable|array',
            // 'permissions.*' => 'exists:permissions,id',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'ADMIN',
        ]);

        // Assign role
        // $role = Role::find($request->role);
        // $user->roles()->attach($role);
        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }

        // Assign permissions to the role
        // if ($request->has('permissions')) {
        //     $role->permissions()->sync($request->permissions);
        // }

        return redirect()->route('admin.users.index')
            ->with('success', 'Admin user created successfully');
    }

    public function edit(User $user)
    {
        if ($user->role !== 'ADMIN') {
            abort(404);
        }
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'You cannot edit permissions on your own account');
        }
        
        $roles = Role::all();
        $permissions = Permission::all();
        $user->load('roles.permissions');
        
        return view('admin.users.edit', compact('user', 'roles', 'permissions'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->role !== 'ADMIN') {
            abort(404);
        }
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'You cannot edit permissions on your own account');
        }
        $request->validate([
            // 'name' => 'required|string|max:255',
            // 'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            // 'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            // 'role' => 'required|exists:roles,id',
            // 'permissions' => 'nullable|array',
            // 'permissions.*' => 'exists:permissions,id'
              'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id'

            
        ]);

        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }
        // $user->update([
        //     'name' => $request->name,
        //     'email' => $request->email,
        // ]);

        // if ($request->password) {
        //     $user->update(['password' => Hash::make($request->password)]);
        // }

        // Sync role
        // $role = Role::find($request->role);
        // $user->roles()->sync([$role->id]);

        // Sync permissions for the role
        // if ($request->has('permissions')) {
        //     $role->permissions()->sync($request->permissions);
        // }

        return redirect()->route('admin.users.index')
            ->with('success', 'Admin user roles updated successfully');
    }

    public function destroy(User $user)
    {
        if ($user->role !== 'ADMIN') {
            abort(404);
        }
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'You cannot delete your own account');
        }

        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'Admin user deleted successfully');
    }
}