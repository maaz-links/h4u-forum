<?php

namespace App\Http\Controllers\Admin\DataFeed;

use App\Http\Controllers\Controller;

use App\Models\SpokenLanguage;
use App\Services\AuditAdmin;
use Illuminate\Http\Request;

class SpokenLanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $spoken_languages = SpokenLanguage::all();
        return view('spoken_languages.index', compact('spoken_languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('spoken_languages.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:spoken_languages,name',
        ]);

        SpokenLanguage::create($request->all());
        AuditAdmin::audit("DataFeed/SpokenLanguageController@store");
        return redirect()->route('spoken-languages.index')
            ->with('success', 'Spoken Language created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SpokenLanguage $spoken_language)
    {
        return view('spoken_languages.show', compact('spoken_language'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SpokenLanguage $spoken_language)
    {
        return view('spoken_languages.form', compact('spoken_language'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SpokenLanguage $spoken_language)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:spoken_languages,name,'.$spoken_language->id,
        ]);

        $spoken_language->update($request->all());
        AuditAdmin::audit("DataFeed/SpokenLanguageController@update");
        return redirect()->route('spoken-languages.index')
            ->with('success', 'Spoken Language updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SpokenLanguage $spoken_language)
    {
        $spoken_language->delete();
        AuditAdmin::audit("DataFeed/SpokenLanguageController@destroy");
        return redirect()->route('spoken-languages.index')
            ->with('success', 'Spoken Language deleted successfully');
    }
}