<?php

namespace App\Http\Controllers\Admin\DataFeed;

use App\Http\Controllers\Controller;

use App\Models\HostessService;
use App\Models\Interest;
use App\Services\AuditAdmin;
use Illuminate\Http\Request;

class HostessServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hostess_services = HostessService::all();
        return view('hostess_services.index', compact('hostess_services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('hostess_services.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:hostess_services,name',
        ]);

        HostessService::create($request->all());
        AuditAdmin::audit("DataFeed/HostessServiceController@store");

        return redirect()->route('hostess-services.index')
            ->with('success', 'Hostess Service created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(HostessService $hostess_service)
    {
        return view('hostess_services.show', compact('hostess_service'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HostessService $hostess_service)
    {
        return view('hostess_services.form', compact('hostess_service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HostessService $hostess_service)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:hostess_services,name,'.$hostess_service->id,
        ]);

        $hostess_service->update($request->all());
        AuditAdmin::audit("DataFeed/HostessServiceController@update");

        return redirect()->route('hostess-services.index')
            ->with('success', 'Hostess Service updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HostessService $hostess_service)
    {
        $hostess_service->delete();
        AuditAdmin::audit("DataFeed/HostessServiceController@destroy");
        return redirect()->route('hostess-services.index')
            ->with('success', 'Hostess Service deleted successfully');
    }
}