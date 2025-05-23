<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::with('roles')->get();
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.permissions.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:permissions',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id'
        ]);

        $permission = Permission::create([
            'name' => $request->name,
            'slug' => $request->slug,
        ]);

        if ($request->has('roles')) {
            $permission->roles()->sync($request->roles);
        }

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission created successfully');
    }

    public function edit(Permission $permission)
    {
        $roles = Role::all();
        return view('admin.permissions.edit', compact('permission', 'roles'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:permissions,slug,'.$permission->id,
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id'
        ]);

        $permission->update([
            'name' => $request->name,
            'slug' => $request->slug,
        ]);

        $permission->roles()->sync($request->roles ?? []);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission updated successfully');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission deleted successfully');
    }
}