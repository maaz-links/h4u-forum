<?php

// app/Http/Controllers/AttachmentController.php
namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        //dd($user->attachments);
        return response()->json($user->attachments);
    }

    public function store(Request $request)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5000',
            'profile_pic_id' => 'nullable|string'
        ]);

        $user = Auth::user();
        $uploadedImages = [];

        foreach ($request->file('images') as $file) {
            $path = $file->store('attachments/' . $user->id, 'local');
            
            $attachment = Attachment::create([
                'user_id' => $user->id,
                'path' => $path,
                'is_profile_picture' => false
            ]);

            $uploadedImages[] = [
                'id' => $attachment->id,
                'url' => route('attachments.show', $attachment->id),
                'is_profile_picture' => false
            ];
        }

        // Update profile picture if specified
        if ($request->profile_pic_id) {
            Attachment::where('user_id', $user->id)->update(['is_profile_picture' => false]);
            Attachment::where('id', $request->profile_pic_id)
                ->where('user_id', $user->id)
                ->update(['is_profile_picture' => true]);
        }

        return response()->json($uploadedImages);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $attachment = Attachment::where('user_id', $user->id)->findOrFail($id);
        
        Storage::disk('local')->delete($attachment->path);
        $attachment->delete();

        return response()->json(['message' => 'Image deleted successfully']);
    }

    public function show($id)
    {
        //$user = Auth::user();
        $attachment = Attachment:://where('user_id', $user->id)->
        findOrFail($id);

        if (!Storage::disk('local')->exists($attachment->path)) {
            abort(404);
        }

        $file = Storage::disk('local')->get($attachment->path);
        $type = Storage::disk('local')->mimeType($attachment->path);

        return response($file, 200)->header('Content-Type', $type);
    }

    public function setProfilePicture(Request $request, $id)
    {
        $user = Auth::user();
        
        // Reset all profile pictures
        Attachment::where('user_id', $user->id)->update(['is_profile_picture' => false]);
        
        // Set new profile picture
        $attachment = Attachment::where('user_id', $user->id)->findOrFail($id);
        $attachment->update(['is_profile_picture' => true]);
        $user->update(['profile_picture_id' => $attachment->id]);

        return response()->json(['message' => 'Profile picture updated successfully']);
    }
}