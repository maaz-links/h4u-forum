<?php

namespace Database\Seeders;

use App\Models\ModerationRule;
use Illuminate\Database\Seeder;

class ModerationRulesSeeder extends Seeder
{
    public function run()
    {
        $rules = [
            // Keywords
            ['type' => 'keyword', 'name' => null, 'pattern' => 'whatsapp'],
            ['type' => 'keyword', 'name' => null, 'pattern' => 'telegram'],
            ['type' => 'keyword', 'name' => null, 'pattern' => 'skype'],
            ['type' => 'keyword', 'name' => null, 'pattern' => 'discord'],
            ['type' => 'keyword', 'name' => null, 'pattern' => 'paypal'],
            ['type' => 'keyword', 'name' => null, 'pattern' => 'venmo'],
            ['type' => 'keyword', 'name' => null, 'pattern' => 'cashapp'],
            ['type' => 'keyword', 'name' => null, 'pattern' => 'western union'],
            ['type' => 'keyword', 'name' => null, 'pattern' => 'moneygram'],
            ['type' => 'keyword', 'name' => null, 'pattern' => 'bitcoin'],
            ['type' => 'keyword', 'name' => null, 'pattern' => 'crypto'],
            ['type' => 'keyword', 'name' => null, 'pattern' => 'facebook'],
            ['type' => 'keyword', 'name' => null, 'pattern' => 'instagram'],
            ['type' => 'keyword', 'name' => null, 'pattern' => 'twitter'],
            ['type' => 'keyword', 'name' => null, 'pattern' => 'tiktok'],
            ['type' => 'keyword', 'name' => null, 'pattern' => 'snapchat'],
            ['type' => 'keyword', 'name' => null, 'pattern' => 'contact me'],
            ['type' => 'keyword', 'name' => null, 'pattern' => 'my number'],
            ['type' => 'keyword', 'name' => null, 'pattern' => 'my email'],
            ['type' => 'keyword', 'name' => null, 'pattern' => 'dm me'],
            ['type' => 'keyword', 'name' => null, 'pattern' => 'private message'],
        
            // Regex Patterns
            ['type' => 'regex', 'name' => 'phone', 'pattern' => '/\+?[\d\s\-\(\)]{7,}|\(\d{3}\)\s?\d{3}[\s\-]?\d{4}|\d{3}[\s\-]?\d{3}[\s\-]?\d{4}/'],
            ['type' => 'regex', 'name' => 'email', 'pattern' => '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/'],
            ['type' => 'regex', 'name' => 'url', 'pattern' => '/((https?|ftp):\/\/)?([\w\-]+\.)+[\w\-]+(\/[\w\- .\/?%&=]*)?/'],
            ['type' => 'regex', 'name' => 'ip_address', 'pattern' => '/\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b/'],
            ['type' => 'regex', 'name' => 'credit_card', 'pattern' => '/\b(?:\d[ -]*?){13,16}\b/'],
            ['type' => 'regex', 'name' => 'scam_keywords', 'pattern' => '/\b(limited time|risk-free|act now|click here)\b/i'],
            ['type' => 'regex', 'name' => 'money_transfer', 'pattern' => '/\b(wire transfer|send money|bank account|routing number)\b/i'],
        ];
        
        foreach ($rules as $rule) {
            $searchCriteria = ['type' => $rule['type']];
            
            if ($rule['type'] !== 'keyword') {
                $searchCriteria['name'] = $rule['name'];
            } else {
                $searchCriteria['pattern'] = $rule['pattern'];
            }

            ModerationRule::firstOrCreate($searchCriteria, $rule);
        }
    }
}