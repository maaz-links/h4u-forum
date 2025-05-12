<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\UserConfig;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function index()
    {
        $configs = UserConfig::orderBy('key')->get();
        return view('configs.index', compact('configs'));
    }

    public function create()
    {
        return view('configs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|unique:user_configs,key',
            'value' => 'required|string',
        ]);

        UserConfig::create($request->only(['key', 'value']));

        return redirect()->route('configs.index')->with('success', 'Configuration created successfully.');
    }

    public function edit(UserConfig $mail_config)
    {
        return view('configs.edit', compact('mail_config'));
    }

    public function update(Request $request, UserConfig $mail_config)
    {
        $request->validate([
            'key' => 'required|string|unique:user_configs,key,' . $mail_config->id,
            'value' => 'required|string',
        ]);

        $mail_config->update($request->only(['key', 'value']));

        return redirect()->route('configs.index')->with('success', 'Configuration updated successfully.');
    }

    public function destroy(UserConfig $mail_config)
    {
        $mail_config->delete();

        return redirect()->route('configs.index')->with('success', 'Configuration deleted successfully.');
    }
}