<?php

namespace App\Services;

class MessageModerator
{
    protected array $rules = [
        'keywords' => ['whatsapp', 'paypal', 'telegram', 'skype'],
        'regex' => [
            'phone' => '/\+?[\d\s\-\(\)]{7,}/',
            'email' => '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/',
            'url' => '/(https?:\/\/)?([\w\-]+\.)+[\w\-]+(\/[\w\- .\/?%&=]*)?/',
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