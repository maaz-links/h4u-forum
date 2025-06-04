<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactRequest;
use App\Services\AuditAdmin;
use Illuminate\Http\Request;

class ContactRequestController extends Controller
{
    public function index(Request $request)
    {
        //$requests = ContactRequest::latest()->paginate(10);
        $query = ContactRequest::latest();

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
        }

        $requests = $query->paginate(10)->appends($request->except('page'));
        return view('contact_requests.index', compact('requests'));
    }

    public function show(ContactRequest $contactRequest)
    {
        return view('contact_requests.show', compact('contactRequest'));
    }

    public function destroy(ContactRequest $contactRequest)
    {
        $contactRequest->delete();
        AuditAdmin::audit("ContactRequestController@destroy");
        return redirect()->route('contact-requests.index')
            ->with('success', 'Contact request deleted successfully');
    }
}