<?php

namespace App\Http\Controllers\Admin\DataFeed;

use App\Http\Controllers\Controller;
use App\Models\FormEyeColor;
use App\Models\UserProfile;
use DB;
use Illuminate\Http\Request;

class FormEyeColorController extends Controller
{
    public function index()
    {
        $form_eye_colors = FormEyeColor::orderBy('name')->get();
        return view('form-eye-colors.index', compact('form_eye_colors'));
    }

    public function create()
    {
        return view('form-eye-colors.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:form_eye_colors,name',
        ]);

        FormEyeColor::create($validated);

        return redirect()->route('form-eye-colors.index')
            ->with('success', 'Eye color created successfully');
    }

    public function edit(FormEyeColor $form_eye_color)
    {
        return view('form-eye-colors.form', compact('form_eye_color'));
    }

    public function update(Request $request, FormEyeColor $form_eye_color)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:form_eye_colors,name,' . $form_eye_color->id,
        ]);

        $changed_eye_color_name = $form_eye_color->name;
        $form_eye_color->update($validated);

        UserProfile::where('eye_color', $changed_eye_color_name)->update([
            'eye_color' => $form_eye_color->name,
        ]);

        return redirect()->route('form-eye-colors.index')
            ->with('success', 'Eye color updated successfully');
    }

    public function destroy(FormEyeColor $form_eye_color)
    {
        $totalEyeColors = FormEyeColor::count();
        if ($totalEyeColors <= 1) {
            return redirect()->route('form-eye-colors.index')
                ->with('error', 'Cannot delete the only remaining eye color.');
        }

        if ($form_eye_color->isDefault()) {
            return redirect()->route('form-eye-colors.index')
                ->with('error', 'Cannot delete the default eye color');
        }

        $deleted_eye_color_name = $form_eye_color->name;
        $form_eye_color->delete();

        $this->replaceWithDefaultValues($deleted_eye_color_name);

        return redirect()->route('form-eye-colors.index')
            ->with('success', 'Eye color deleted successfully');
    }

    public function setDefault(FormEyeColor $form_eye_color)
    {
        $this->putDefaultInDatabase($form_eye_color);

        return redirect()->route('form-eye-colors.index')
            ->with('success', $form_eye_color->name . ' is now the default eye color.');
    }

    protected function putDefaultInDatabase(FormEyeColor $form_eye_color)
    {
        DB::transaction(function () use ($form_eye_color) {
            FormEyeColor::where('is_default', true)->update(['is_default' => false]);
            $form_eye_color->update(['is_default' => true]);
        });
    }

    protected function replaceWithDefaultValues($deleted_eye_color_name)
    {
        $defaultEyeColor = FormEyeColor::where('is_default', true)->first();

        if ($defaultEyeColor) {
            UserProfile::where('eye_color', $deleted_eye_color_name)->update([
                'eye_color' => $defaultEyeColor->name,
            ]);
        }
    }
}
