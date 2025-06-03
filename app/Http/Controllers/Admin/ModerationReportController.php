<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MessageAlert;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModerationReportController extends Controller
{
    public function index(Request $request)
    {
        $timeRange = $request->input('time_range', 'week');
        
        return view('admin.moderation-reports', [
            'timeRange' => $timeRange,
            'violationStats' => $this->getViolationStatistics($timeRange),
            'userStats' => $this->getUserStatistics($timeRange),
            // 'statusStats' => $this->getStatusStatistics($timeRange),
            'timeRanges' => [
                'day' => 'Last 24 Hours',
                'week' => 'Last 7 Days',
                'month' => 'Last 30 Days',
                'quarter' => 'Last 3 Months',
                'year' => 'Last Year',
                'all' => 'All Time'
            ]
        ]);
    }

    protected function getViolationStatistics($range)
    {
        $alerts = MessageAlert::where('created_at', '>=', $this->getRangeDate($range))
            ->get();
            
        $violations = [];
        
        foreach ($alerts as $alert) {
            if (is_array($alert->detected_rules)) {
                foreach ($alert->detected_rules as $rule) {
                    if (!isset($violations[$rule])) {
                        $violations[$rule] = 0;
                    }
                    $violations[$rule]++;
                }
            }
        }
        
        arsort($violations);
        
        return collect(array_slice($violations, 0, 10, true))
            ->map(function($count, $rule) {
                return (object) ['rule' => $rule, 'count' => $count];
            })
            ->values();
    }

    protected function getUserStatistics($range)
    {
        return MessageAlert::with('user')
            ->where('status', '!=', 'PENDING')
            ->where('created_at', '>=', $this->getRangeDate($range))
            ->selectRaw("user_id, COUNT(*) as alert_count")
            ->groupBy('user_id')
            ->orderByDesc('alert_count')
            ->limit(10)
            ->get()
            ->map(function($item) {
                $item->user_name = $item->user->name ?? 'Deleted User';
                return $item;
            });
    }

    protected function getStatusStatistics($range)
    {
        return MessageAlert::where('created_at', '>=', $this->getRangeDate($range))
            ->selectRaw("status, COUNT(*) as count")
            ->groupBy('status')
            ->orderByDesc('count')
            ->get();
    }

    protected function getRangeDate($range)
    {
        return match($range) {
            'day' => now()->subDay(),
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'quarter' => now()->subQuarter(),
            'year' => now()->subYear(),
            default => null
        };
    }
}