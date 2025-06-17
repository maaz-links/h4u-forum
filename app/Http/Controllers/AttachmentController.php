<?php

// app/Http/Controllers/AttachmentController.php
namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Validator;

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
        $user = Auth::user();
        $maxAttachments = config('h4u.attachments.limit');

        // Check if the user has already reached the attachment limit
        $existingAttachmentsCount = Attachment::where('user_id', $user->id)->count();
        
        if ($existingAttachmentsCount >= $maxAttachments) {
            return response()->json([
                'message' => 'You have reached the maximum limit of ' . $maxAttachments . ' images.'
            ], 422);
        }
        
        $validator = Validator::make($request->all(), [
            'images' => 'required|array|size:1',
            'images.*' => 'image|max:10000',
        ], [
            'images.size' => 'Only one image can be uploaded at a time.',
            'images.*.image' => 'The uploaded file must be an image.',
            'images.*.max' => 'The image must not be larger than 5MB.',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422); // Unprocessable Entity
        }
        //dd('ok');
        $user = Auth::user();
        $uploadedImages = [];
        $imageService = new ImageService();
        foreach ($request->file('images') as $file) {
            //$path = $file->store('attachments/' . $user->id, 'local');
            
            
            $path = $imageService->processAndSaveImage(
                $file,
                'attachments/' . $user->id,
                true,
            );
    
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
        $user = $request->user();
        
        // Set new profile picture
        $attachment = Attachment::where('user_id', $user->id)->findOrFail($id);
        $user->update(['profile_picture_id' => $attachment->id]);

        return response()->json(['message' => 'Profile picture updated successfully']);
    }
}