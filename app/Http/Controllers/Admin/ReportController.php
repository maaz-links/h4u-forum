<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of the reports.
     */
    public function index()
    {
        $reports = Report::with(['reporter', 'reportedUser'])
                        ->latest()
                        ->paginate(10);
                        
        return view('reported-users.index', compact('reports'));
    }

    /**
     * Display the specified report.
     */
    public function show(Report $report)
    {
        $report->load(['reporter', 'reportedUser']);
        return view('reported-users.show', compact('report'));
    }

    /**
     * Remove the specified report from storage.
     */
    public function destroy(Report $report)
    {
        $report->delete();
        
        return redirect()->route('reports.index')
                        ->with('success', 'Report deleted successfully');
    }
}