<?php
// app/Http/Controllers/MailConfigController.php

namespace App\Http\Controllers\Admin;

use App\Models\UserConfig;
use App\Services\AuditAdmin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentConfigController extends Controller
{
    public function edit()
    {
        return view('payment-config.edit', [
        ]);
    }

    public function update(Request $request)
    {
        //dd($request);
        $validated = $request->validate([
            'services_paypal_client_id' => 'required|string',
            'services_paypal_secret' => 'required|string',
            'services_paypal_sandbox' => 'required|boolean',
            'services_stripe_key' => 'required|string',
            'services_stripe_secret' => 'required|string',
        ]);

        UserConfig::updateOrCreate(
            ['key' => 'services.paypal.client_id'],
            ['value' => $request->services_paypal_client_id]
        );
        UserConfig::updateOrCreate(
            ['key' => 'services.paypal.secret'],
            ['value' => $request->services_paypal_secret]
        );
        UserConfig::updateOrCreate(
            ['key' => 'services.paypal.sandbox'],
            ['value' => $request->services_paypal_sandbox]
        );
        UserConfig::updateOrCreate(
            ['key' => 'services.stripe.key'],
            ['value' => $request->services_stripe_key]
        );
        UserConfig::updateOrCreate(
            ['key' => 'services.stripe.secret'],
            ['value' => $request->services_stripe_secret]
        );

        AuditAdmin::audit("PaymentConfigController@update");
        
        return redirect()->route('payment-config.edit')
            ->with('success', 'Payment configuration updated successfully!');
    }
}