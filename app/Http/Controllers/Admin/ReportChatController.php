<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\ReportChat;
use App\Services\AuditAdmin;
use Illuminate\Http\Request;

class ReportChatController extends Controller
{
    /**
     * Display a listing of the reports.
     */
    public function index()
    {
        $reports = ReportChat::with(['reporter',
         'reportedChat' => function ($query) {
            $query->with([
                'user1' => function($query)  {
                    $query->select('id','name');
                },
                'user2' => function($query)  {
                    $query->select('id','name');
                }
            ]);
         }
         ])
                        ->latest()
                        ->paginate(10);
                        
        return view('reported-chats.index', compact('reports'));
    }

    /**
     * Display the specified report.
     */
    public function show(Report $report)
    {
        $report->load(['reporter', 'reportedChat']);
        return view('reported-chats.show', compact('report'));
    }

    /**
     * Remove the specified report from storage.
     */
    public function destroy(Report $report)
    {
        $report->delete();
        AuditAdmin::audit("ReportChatController@destroy");
        
        return redirect()->route('reports.index')
                        ->with('success', 'Report deleted successfully');
    }
}