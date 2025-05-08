<?php

namespace App\Listeners;

use App\Events\ProfileViewed;
use App\Models\ProfileView;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecordProfileView implements ShouldQueue // Optional: implement ShouldQueue for async processing
{
    public function handle(ProfileViewed $event)
    {
        // Check if a view exists in the last 24 hours
        $recentViewExists = ProfileView::where('viewer_id', $event->viewerId)
            ->where('viewed_id', $event->viewedId)
            ->where('created_at', '>', now()->subDay())
            ->exists();

        if (!$recentViewExists) {
            ProfileView::where('viewer_id', $event->viewerId)
                ->where('viewed_id', $event->viewedId)
                ->delete();
            
            ProfileView::create([
                'viewer_id' => $event->viewerId,
                'viewed_id' => $event->viewedId
            ]);
        }
    }
}