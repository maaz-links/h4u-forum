<?php

namespace App\Http\Controllers\Admin;

use App\Models\ModerationRule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class ModerationRuleController extends Controller
{
    public function index(Request $request)
    {
        $query = ModerationRule::latest();
        //$search = $request->input('search');
        
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;

            $query->where('type', 'like', "%{$searchTerm}%")
            ->orWhere('name', 'like', "%{$searchTerm}%")
            ->orWhere('pattern', 'like', "%{$searchTerm}%");
        }

        $rules = $query->paginate(10)->appends($request->except('page'));
        
        return view('moderation-rules.index', compact('rules'));
    }

    public function create()
    {
        return view('moderation-rules.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:keyword,regex',
            'name' => 'required_if:type,regex|nullable|string|max:255',
            'pattern' => 'required|string|max:1000'
        ]);
        
        ModerationRule::create($validated);
        
        return redirect()->route('moderation-rules.index')
                         ->with('success', 'Rule created successfully.');
    }

    public function edit(ModerationRule $moderationRule)
    {
        return view('moderation-rules.edit', compact('moderationRule'));
    }

    public function update(Request $request, ModerationRule $moderationRule)
    {
        $validated = $request->validate([
            'type' => 'required|in:keyword,regex',
            'name' => 'required_if:type,regex|nullable|string|max:255',
            'pattern' => 'required|string|max:1000'
        ]);
        
        $moderationRule->update($validated);
        
        return redirect()->route('moderation-rules.index')
                         ->with('success', 'Rule updated successfully.');
    }

    public function destroy(ModerationRule $moderationRule)
    {
        $moderationRule->delete();
        
        return redirect()->route('moderation-rules.index')
                         ->with('success', 'Rule deleted successfully.');
    }
}