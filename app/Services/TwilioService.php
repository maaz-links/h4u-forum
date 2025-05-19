<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $this->from = config('services.twilio.from');
        
        $this->client = new Client($sid, $token);
    }

    public function sendSms($to, $message)
    {
        try {
            return $this->client->messages->create($to, [
                'from' => $this->from,
                'body' => $message
            ]);
        } catch (\Exception $e) {
            \Log::error("Twilio SMS send failed", [
                'to' => $to,
                'message' => $message,
                'error' => $e->getMessage()
            ]);
            // Optionally return null or a custom response object
            return null;
        }
    }
    
}