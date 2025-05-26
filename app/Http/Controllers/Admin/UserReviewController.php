<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\User;
use App\Services\AuditAdmin;
use Illuminate\Http\Request;

class UserReviewController extends Controller
{
    public function allreviews(string $name){
        $user = User::select('id','name','role')->forUsername($name)->forRoleAny()->first();
        if(!$user){
            abort(404);
        }
        $reviews =  Review::with('reviewer')->where('reviewed_user_id', $user->id)->get();
        return view('user-profile.reviews',compact('reviews','name'));
    }

    public function deleteReview(Request $request){
        $validated_data = $request->validate([
            'review_id' => 'required|numeric|exists:reviews,id',
            //'admin_reason' => 'required|string',
        ]);

        $review = Review::where('id', $validated_data['review_id'])
        ->with([
            'reviewer' => function($query) {
                $query->select('id','name','role','profile_picture_id');
            },
            'reviewedUser' => function($query) {
                $query->select('id','name','role','profile_picture_id');
            },
        ])
        ->first();

        //return $review;
        $review->delete();
        AuditAdmin::audit("UserReviewController@deleteReview");

        return back()->with("success","Review deleted");
    }

    public function editReview(Review $review){
        $review->load([
            'reviewer' => function($query) {
                $query->select('id','name','role','profile_picture_id');
            },
            'reviewedUser' => function($query) {
                $query->select('id','name','role','profile_picture_id');
            },
        ]);
        //$user = $review->reviewedUser;
        //dd($review);
        return view("user-profile.reviews-edit",compact("review"));
    }

    public function updateReview(Request $request){
        $validated_data = $request->validate([
            'review_id' => 'required|numeric|exists:reviews,id',
            'rating' => 'required|integer|between:1,5',
            //'admin_reason' => 'required|string',
        ]);
        //$review = Review::where('id',$validated_data['review_id'])
        

        $review = Review::where('id', $validated_data['review_id'])
        ->with([
            'reviewer' => function($query) {
                $query->select('id','name','role','profile_picture_id');
            },
            'reviewedUser' => function($query) {
                $query->select('id','name','role','profile_picture_id');
            },
        ])->first();
        
        $review->update(['rating' => $validated_data['rating']]);;
        AuditAdmin::audit("UserReviewController@updateReview");
        // AuditAdmin::audit(
        //     "Modified review of " . 
        //         ($review->reviewedUser->name ?? 'Unknown User') . " (ID: " . ($review->reviewedUser->id ?? 'N/A') . ") by ".   ($review->reviewer->name ?? '[deleted]') . " (ID: " . ($review->reviewer->id ?? 'N/A') . ")",
        //     $request->admin_reason
        // );
        return back()->with("success","Review modified");
    }
}
