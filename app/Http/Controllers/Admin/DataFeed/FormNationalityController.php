<?php

namespace App\Http\Controllers\Admin\DataFeed;

use App\Http\Controllers\Controller;
use App\Models\FormNationality;
use App\Models\UserProfile;
use DB;
use Illuminate\Http\Request;

class FormNationalityController extends Controller
{
    public function index()
    {
        $form_nationalities = FormNationality::orderBy('name')->get();
        return view('form-nationalities.index', compact('form_nationalities'));
    }

    public function create()
    {
        return view('form-nationalities.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:form_nationalities,name',
        ]);

        FormNationality::create($validated);

        return redirect()->route('form-nationalities.index')
            ->with('success', 'Nationality created successfully');
    }

    public function edit(FormNationality $form_nationality)
    {
        return view('form-nationalities.form', compact('form_nationality'));
    }

    public function update(Request $request, FormNationality $form_nationality)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:form_nationalities,name,'.$form_nationality->id,
        ]);

        $changed_nationality_name = $form_nationality->name;
        $form_nationality->update($validated);

        UserProfile::where('nationality',$changed_nationality_name)->update([
            'nationality'  => $form_nationality->name,
        ]);

        return redirect()->route('form-nationalities.index')
            ->with('success', 'Nationality updated successfully');
    }

    public function destroy(FormNationality $form_nationality)
    {
        
        $totalnationalities = FormNationality::count();
        //dd($totalnationalities);
        if ($totalnationalities <= 1) {
            return redirect()->route('form-nationalities.index')
                ->with('error', 'Cannot delete the only remaining nationality.');
        }

        if($form_nationality->isDefault()){
            return redirect()->route('form-nationalities.index')
            ->with('error', 'Cannot delete the default nationality');
        }
        $deleted_nationality_name = $form_nationality->name;
        $form_nationality->delete();

        $this->replaceWithDefaultValues($deleted_nationality_name);

        return redirect()->route('form-nationalities.index')
            ->with('success', 'Nationality deleted successfully');
    }

    public function setDefault(FormNationality $form_nationality){
        
        $this->putDefaultInDatabase($form_nationality);

        return redirect()->route('form-nationalities.index')
            ->with('success', $form_nationality->name.' is now default nationality.');
    }

    protected function putDefaultInDatabase(FormNationality $form_nationality){
        
            DB::transaction(function () use ($form_nationality) {
                FormNationality::where('is_default', true)->update(['is_default' => false]);
                $form_nationality->update(['is_default' => true]);
            });
        
    }

    protected function replaceWithDefaultValues($deleted_nationality_name){

        $defaultNat = FormNationality::where('is_default', true)->first();

        if ($defaultNat) {

            UserProfile::where('nationality',$deleted_nationality_name)->update([
                'nationality'  => $defaultNat->name,
            ]);
        }
    }
}
