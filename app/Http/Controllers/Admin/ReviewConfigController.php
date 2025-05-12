<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserConfig;
use App\Services\AuditAdmin;
use Illuminate\Http\Request;

class ReviewConfigController extends Controller
{
    public function index()
    {
        $configs = UserConfig::orderBy('key')->get();
        return view('reviews-config.index', compact('configs'));
    }

    public function store(Request $request){

        //dd($request);
        $validated_data = $request->validate([
            'review_delay' => 'nullable|numeric|min:0|max:10000|required_without_all:minimum_reviews_for_visibility,minimum_rating',
            'minimum_reviews_for_visibility' => 'nullable|numeric|min:0|max:10000|required_without_all:review_delay,minimum_rating',
            'minimum_rating' => 'nullable|numeric|min:1|max:5|required_without_all:review_delay,minimum_reviews_for_visibility',
        ],[
            'review_delay.required_without_all'=> 'Input field is required',
            'minimum_reviews_for_visibility.required_without_all'=> 'Input field is required',
            'minimum_rating.required_without_all'=> 'Input field is required',
        ]);

        

        $validated2 = $request->validate([
            'admin_reason' => 'required|string',
        ],);

        //dd($validated_data);

        \DB::transaction(function () use ($validated_data,$request) {
            $results = [];
            
            foreach ($validated_data as $key => $value) {
                $results[$key] = UserConfig::updateOrCreate(
                    ['key' => "h4u.reviews.{$key}"],
                    ['value' => $value]
                );

                $label = ucwords(str_replace('_', ' ', $key));
                $messages[] = "Modified \"$label\" to \"$value\"";
            }
            
            
            AuditAdmin::audit($messages[0],$request->admin_reason);

            return $results;
        });
        return redirect()->route('reviews-config.index')->with('success', 'Review Configs modified successfully.');
    }
}
