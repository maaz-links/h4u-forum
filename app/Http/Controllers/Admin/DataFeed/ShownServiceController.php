<?php

namespace App\Http\Controllers\Admin\DataFeed;

use App\Http\Controllers\Controller;
use App\Models\ShownService;
use App\Services\AuditAdmin;
use Illuminate\Http\Request;
use Storage;

class ShownServiceController extends Controller
{
    public function index()
    {
        $services = ShownService::orderBy('display_order')->get();
        return view('shown_services.index', compact('services'));
    }

    public function create()
    {
        return view('shown_services.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'image' => 'required|image|max:2048',
            'display_order' => 'required|integer',
        ]);

        $imagePath = $request->file('image')->store('shown-services', 'public');

        ShownService::create([
            'name' => $request->name,
            'image_path' => $imagePath,
            'display_order' => $request->display_order,
        ]);
        AuditAdmin::audit("ShownServiceController@store");
        return redirect()->route('shown-services.index')
            ->with('success', 'Service created successfully.');
    }

    public function show(ShownService $shownService)
    {
        return view('shown_services.show', compact('shownService'));
    }

    public function edit(ShownService $shownService)
    {
        return view('shown_services.form', compact('shownService'));
    }

    public function update(Request $request, ShownService $shownService)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'display_order' => 'required|integer',
        ]);

        $data = [
            'name' => $request->name,
            'display_order' => $request->display_order,
        ];

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($shownService->image_path) {
                Storage::disk('public')->delete($shownService->image_path);
            }
            $data['image_path'] = $request->file('image')->store('shown-services', 'public');
        }

        $shownService->update($data);
        AuditAdmin::audit("ShownServiceController@update");
        return redirect()->route('shown-services.index')
            ->with('success', 'Service updated successfully');
    }

    public function destroy(ShownService $shownService)
    {
        if ($shownService->image_path) {
            Storage::disk('public')->delete($shownService->image_path);
        }
        
        $shownService->delete();
        AuditAdmin::audit("ShownServiceController@destroy");
        return redirect()->route('shown-services.index')
            ->with('success', 'Service deleted successfully');
    }
}