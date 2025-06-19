<?php

namespace App\Console\Commands;

use App\Events\ReviewRemainder;
use App\Models\Chat;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendReviewReminders extends Command
{
    protected $signature = 'reminders:send-feedback';
    protected $description = 'Send feedback reminders for inactive chats';

    //Gets all chat that have been unlocked for 3 days. and send_feedback_reminder is 1 (not sent reminder yet).
    //Update send_feedback_reminder to 0.
    //Then only send notification if user is KING and has not given review already.
    public function handle()
    {
        $count = 0;

        Chat::unlockedForDays(Carbon::now()->subDays(config('h4u.reviews.review_delay')))
            ->where('send_feedback_reminder', 1)
            ->with(['user1', 'user2'])
            ->chunkById(100, function ($chats) use (&$count) {
                foreach ($chats as $chat) {
                    $count += $this->processChat($chat);
                    $chat->update(['send_feedback_reminder' => 0]); // prevent duplicate reminders
                }
            });

        $this->info("Sent {$count} feedback reminders.");
    }

    private function processChat($chat): int
    {
        $remindersSent = 0;

        if ($this->shouldSendReminder($chat->user1, $chat->user2)) {
            event(new ReviewRemainder($chat->user2, $chat->user1));
            $remindersSent++;
        }

        if ($this->shouldSendReminder($chat->user2, $chat->user1)) {
            event(new ReviewRemainder($chat->user1, $chat->user2));
            $remindersSent++;
        }

        return $remindersSent;
    }

    private function shouldSendReminder(User $reviewer, User $reviewed): bool
    {
        if ($reviewer->role !== User::ROLE_KING) {
            return false;
        }

        return !Review::where('reviewer_id', $reviewer->id)
                      ->where('reviewed_user_id', $reviewed->id)
                      ->exists();
    }
}
