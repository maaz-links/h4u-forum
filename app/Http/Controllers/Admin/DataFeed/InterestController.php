<?php

namespace App\Http\Controllers\Admin\DataFeed;

use App\Http\Controllers\Controller;

use App\Models\Interest;
use App\Services\AuditAdmin;
use Illuminate\Http\Request;

class InterestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $interests = Interest::all();
        return view('interests.index', compact('interests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('interests.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:interests,name',
        ]);

        Interest::create($request->all());
        AuditAdmin::audit("DataFeed/InterestController@store");

        return redirect()->route('interests.index')
            ->with('success', 'Interest created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Interest $interest)
    {
        return view('interests.show', compact('interest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Interest $interest)
    {
        return view('interests.form', compact('interest'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Interest $interest)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:interests,name,'.$interest->id,
        ]);

        $interest->update($request->all());
        AuditAdmin::audit("DataFeed/InterestController@update");
        return redirect()->route('interests.index')
            ->with('success', 'Interest updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Interest $interest)
    {
        $interest->delete();
        AuditAdmin::audit("DataFeed/InterestController@destroy");
        return redirect()->route('interests.index')
            ->with('success', 'Interest deleted successfully');
    }
}