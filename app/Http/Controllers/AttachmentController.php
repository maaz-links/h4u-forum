<?php

// app/Http/Controllers/AttachmentController.php
namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Str;

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
            'images.*' => 'image|max:5000',
            'profile_pic_id' => 'nullable|string'
        ]);
        //dd('ok');
        $user = Auth::user();
        $uploadedImages = [];

        foreach ($request->file('images') as $file) {
            //$path = $file->store('attachments/' . $user->id, 'local');
            
            $manager = new ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );
            $image = $manager->read($file->getRealPath());
            $image->scale(1000, 1000);
            // Process the image
            //$image->resize(800, 800);
            $encoded = $image->toWebp();
            $path = 'attachments/' . $user->id . '/' . Str::uuid()->toString() . '.webp';
            $encoded->save(storage_path('app/private/'. $path));
            //dd($encoded);
        // ->resize(800, 800, function ($constraint) {
        //     $constraint->aspectRatio();
        //     $constraint->upsize(); // Prevent upsizing smaller images
        // })
        // ->encode('webp', 75); // Convert to WebP with 75% quality
            //dd('ender');
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
        $user = $request->user();
        
        // Reset all profile pictures
        // Attachment::where('user_id', $user->id)->update(['is_profile_picture' => false]);
        
        // Set new profile picture
        $attachment = Attachment::where('user_id', $user->id)->findOrFail($id);
        // $attachment->update(['is_profile_picture' => true]);
        $user->update(['profile_picture_id' => $attachment->id]);

        return response()->json(['message' => 'Profile picture updated successfully']);
    }
}