<?php
// app/Http/Controllers/MailConfigController.php

namespace App\Http\Controllers\Admin;

use App\Models\UserConfig;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MailConfigController extends Controller
{
    public function edit()
    {
        // Define the mail keys we want to edit
        // $mailKeys = [
        //     'mail.default',
        //     'mail.from.address',
        //     'mail.from.name',
        //     'mail.mailers.smtp.host',
        //     'mail.mailers.smtp.port',
        //     'mail.mailers.smtp.encryption',
        //     'mail.mailers.smtp.username',
        //     'mail.mailers.smtp.password',
        // ];
        
        // Fetch existing values
        // $configs = UserConfig::whereIn('key', $mailKeys)
        //     ->pluck('value', 'key')
        //     ->toArray();
            
        return view('mail-config.edit', [
            // 'configs' => $configs,
            // 'mailKeys' => $mailKeys
        ]);
    }

    public function update(Request $request)
    {
        //dd($request);
        $validated = $request->validate([
            'mail_default' => 'required|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
            'mail_smtp_host' => 'required|string',
            'mail_smtp_port' => 'required|numeric',
            'mail_smtp_encryption' => 'required|string',
            'mail_smtp_username' => 'required|string',
            'mail_smtp_password' => 'required|string',
        ]);
        
        UserConfig::updateOrCreate(
            ['key' => 'mail.default'],
            ['value' => $request->mail_default]
        );
        UserConfig::updateOrCreate(
            ['key' => 'mail.from.address'],
            ['value' => $request->mail_from_address]
        );
        UserConfig::updateOrCreate(
            ['key' => 'mail.from.name'],
            ['value' => $request->mail_from_name]
        );
        UserConfig::updateOrCreate(
            ['key' => 'mail.mailers.smtp.host'],
            ['value' => $request->mail_smtp_host]
        );
        UserConfig::updateOrCreate(
            ['key' => 'mail.mailers.smtp.port'],
            ['value' => $request->mail_smtp_port]
        );
        UserConfig::updateOrCreate(
            ['key' => 'mail.mailers.smtp.encryption'],
            ['value' => $request->mail_smtp_encryption]
        );
        UserConfig::updateOrCreate(
            ['key' => 'mail.mailers.smtp.username'],
            ['value' => $request->mail_smtp_username]
        );
        UserConfig::updateOrCreate(
            ['key' => 'mail.mailers.smtp.password'],
            ['value' => $request->mail_smtp_password]
        );
        
        return redirect()->route('mail-config.edit')
            ->with('success', 'Mail configuration updated successfully!');
    }
}