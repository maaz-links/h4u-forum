<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\AuditAdmin;
use App\Services\TextPurifier;
use Illuminate\Http\Request;

class PageController extends Controller
{
    //Use php artisan db:seed --class=PagesTableSeeder
    public function edit($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();
        return view('pages.edit', compact('page'));
    }

    public function update(Request $request, $slug)
    {
        //dd($request);
        $request->validate([
            'content' => 'required',
        ]);
        //dd($request->content);
        $page = Page::where('slug', $slug)->firstOrFail();
        $page->update(['content' => $request->content]);

        AuditAdmin::audit("PageController@update");

        return redirect()->route('pages.edit', $slug)
                         ->with('success', ucwords(str_replace('-', ' ', $page->slug))." updated successfully.");
    }

    public function show($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();
        return view('pages.show', compact('page'));
    }
}
