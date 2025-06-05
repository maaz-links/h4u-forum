<?php

namespace App\Services;

use App\Models\ModerationRule;

class MessageModerator
{
    protected $rules;

    public function __construct()
    {
        $this->rules = ModerationRule::where('is_active', true)->get();
    }

    public function analyze(string $message): array
    {
        $violations = [];
        
        foreach ($this->rules as $rule) {
            if ($rule->type === ModerationRule::TYPE_KEYWORD) {
                // if (stripos($message, $rule->pattern) !== false) {
                //     $violations[] = "Prohibited keyword: {$rule->pattern}";
                // }
                $pattern = $rule->pattern;
                // Check if keyword is surrounded by quotes
                if (str_starts_with($pattern, '"') && str_ends_with($pattern, '"')) {
                    // Remove the surrounding quotes
                    $raw = trim($pattern, '"');
                    $escaped = preg_quote($raw, '/');
                    // Match as a whole word
                    if (preg_match("/\b{$escaped}\b/i", $message)) {
                        $violations[] = "Prohibited keyword: {$raw}";
                    }
                } else {
                    // Normal substring match
                    if (stripos($message, $pattern) !== false) {
                        $violations[] = "Prohibited keyword: {$pattern}";
                    }
                }
            } elseif ($rule->type === ModerationRule::TYPE_REGEX) {
                if (preg_match($rule->pattern, $message)) {
                    $violations[] = "Prohibited pattern detected: {$rule->name}";
                }
            }
        }
        
        return $violations;
    }
}