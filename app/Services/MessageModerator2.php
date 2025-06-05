<?php

namespace App\Services;

class MessageModerator2
{
    protected array $rules = [
        'keywords' => [
            // Messaging platforms
            'whatsapp', 'telegram', 'skype', 'signal', 'wechat', 'viber', 'line', 'kik', 'discord',
            // Payment services
            'paypal', 'venmo', 'cashapp', 'zelle', 'western union', 'moneygram', 'bitcoin', 'crypto',
            // Social media
            'facebook', 'instagram', 'twitter', 'tiktok', 'snapchat',
            // Other common exchange attempts
            'contact me', 'my number', 'my email', 'dm me', 'private message',
            //Bad keywords
            'fuck', 'shit', 'asshole', 'bitch', 'cunt','nigga', 'nigger', 'fag', 
            'retard', 'whore', 'slut', 'damn', 'hell', 'bastard', 'dick',
            'piss', 'cock', 'pussy', 'faggot', 'kys', 'kill yourself'
        ],

        'regex' => [
                // More comprehensive phone number matching
                'phone' => '/\+?[\d\s\-\(\)]{7,}|\(\d{3}\)\s?\d{3}[\s\-]?\d{4}|\d{3}[\s\-]?\d{3}[\s\-]?\d{4}/',
                
                // Existing email pattern is good
                'email' => '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/',
                
                // More comprehensive URL pattern
                'url' => '/((https?|ftp):\/\/)?([\w\-]+\.)+[\w\-]+(\/[\w\- .\/?%&=]*)?/',
                
                // Add new patterns:
                'ip_address' => '/\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b/',
                'credit_card' => '/\b(?:\d[ -]*?){13,16}\b/',

                'scam_keywords' => '/\b(limited time|risk-free|act now|click here)\b/i',
                'money_transfer' => '/\b(wire transfer|send money|bank account|routing number)\b/i'
            ]
    ];

    public function analyze(string $message): array
    {
        $violations = [];
        
        // Check keywords
        foreach ($this->rules['keywords'] as $keyword) {
            if (stripos($message, $keyword) !== false) {
                $violations[] = "Prohibited keyword: $keyword";
            }
        }
        
        // Check regex patterns
        foreach ($this->rules['regex'] as $type => $pattern) {
            if (preg_match($pattern, $message)) {
                $violations[] = "Prohibited pattern detected: $type";
            }
        }
        
        return $violations;
    }
}