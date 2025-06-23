<?php

namespace App\Http\Controllers;

use App\Models\ContactRequest;
use App\Models\EuropeCountry;
use App\Models\Faq;
use App\Models\FormEyeColor;
use App\Models\FormNationality;
use App\Models\HostessService;
use App\Models\Interest;
use App\Models\Page;
use App\Models\SpokenLanguage;

use Illuminate\Http\Request;
use Validator;

class MiscController extends Controller
{
    public function miscdata(Request $request){
        $interests = Interest::all();
        $services = HostessService::all();
        $languages = SpokenLanguage::all();
        $countries = EuropeCountry::with(['provinces' => function($query) {
            $query->ordered(); // Uses the ordered() scope from the Province model
        }])->ordered() // Orders the countries by display_order
           ->get();
        $nationalities = FormNationality::select('name')->ordered()->pluck('name')->toArray();
        $eye_colors = FormEyeColor::select('name')->ordered()->pluck('name')->toArray();
        return response()->json([
            'interests' => $interests,
            'available_for' => $services,
            'spoken_languages' => $languages,
            'countries' => $countries,
            'nationalities' => $nationalities,
            'eye_colors' => $eye_colors,
            'profile_costs' => [
             'standard' => config('h4u.chatcost.standard'),
             'top' => config('h4u.chatcost.topprofile'),
             'verified' => config('h4u.chatcost.verified'),
             'verified_top' => config('h4u.chatcost.verified_topprofile'),
            ]
          ]);
    }


    public function apiGetTerms(Request $request){
        $page = Page::where('slug', 'terms-and-conditions')->firstOrFail();
        return response()->json(['page' => $page], 200);
    }
    public function apiGetPrivacy(Request $request){
        $page = Page::where('slug', 'privacy-policy')->firstOrFail();
        return response()->json(['page' => $page], 200);
    }

    public function apiGetCookiesInfo(Request $request){
        $page = Page::where('slug', 'cookies-info')->firstOrFail();
        return response()->json(['page' => $page], 200);
    }
    public function apiGetPaymentsCredits(Request $request){
        $page = Page::where('slug', 'credits-and-payment')->firstOrFail();
        return response()->json(['page' => $page], 200);
    }

    public function apiContactForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:2000',
            //'termsAccepted' => 'required|accepted'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'formError' => $validator->errors()
            ], 422);
        }

        $contact = ContactRequest::create([
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
            //'terms_accepted' => $request->termsAccepted
        ]);

        return response()->json([
            'message' => 'Contact request submitted successfully',
            'data' => $contact
        ], 201);
    }
    public function apiGetFaqs(Request $request){
        $faqs = Faq::all();
        return response()->json(['faqs' => $faqs], 200);
    }
}
