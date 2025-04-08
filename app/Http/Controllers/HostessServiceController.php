<?php

namespace App\Http\Controllers;

use App\Models\HostessService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HostessServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = HostessService::get();
        return response()->json($services);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:hostess_services',
            'display_order' => 'integer'
        ]);

        $service = HostessService::create($validated);
        return response()->json($service, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(HostessService $hostessService)
    {
        return response()->json($hostessService);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HostessService $hostessService)
    {
        $validated = $request->validate([
            'name' => [
                'sometimes',
                'string',
                'max:100',
                Rule::unique('hostess_services')->ignore($hostessService->id)
            ],
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'display_order' => 'integer'
        ]);

        $hostessService->update($validated);
        return response()->json($hostessService);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HostessService $hostessService)
    {
        $hostessService->delete();
        return response()->json(null, 204);
    }

    // /**
    //  * Restore the specified soft-deleted resource.
    //  */
    // public function restore($id)
    // {
    //     $service = HostessService::withTrashed()->findOrFail($id);
    //     $service->restore();
    //     return response()->json($service);
    // }

    // /**
    //  * Permanently delete the specified resource.
    //  */
    // public function forceDelete($id)
    // {
    //     $service = HostessService::withTrashed()->findOrFail($id);
    //     $service->forceDelete();
    //     return response()->json(null, 204);
    // }
}