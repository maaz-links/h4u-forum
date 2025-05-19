<?php
// app/Http/Controllers/Admin/EmailTemplatesController.php
namespace App\Http\Controllers\Admin;

use App\Models\UserConfig;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmailTemplatesController extends Controller
{
    public function index()
    {
        $templates = config('h4u.emailmessage');
        $subjects = config('h4u.emailsubject');
        
        return view('admin.email-templates.index', compact('templates', 'subjects'));
    }

    public function edit($type)
    {
        if (!array_key_exists($type, config('h4u.emailmessage'))) {
            abort(404);
        }

        $template = config('h4u.emailmessage')[$type];
        $subject = config('h4u.emailsubject')[$type];
        
        return view('admin.email-templates.edit', compact('type', 'template', 'subject'));
    }

    public function update(Request $request, $type)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'template' => 'required|string',
        ]);

        // Update in database if exists, or create new
        UserConfig::updateOrCreate(
            ['key' => "h4u.emailsubject.$type"],
            ['value' => $request->subject]
        );

        UserConfig::updateOrCreate(
            ['key' => "h4u.emailmessage.$type"],
            ['value' => $request->template]
        );

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Template updated successfully');
    }
}