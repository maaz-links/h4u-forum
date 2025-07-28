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
        $maxBatchSize = 100; // Maximum images per upload
    
        // Check total attachment limit
        $existingAttachmentsCount = Attachment::where('user_id', $user->id)->count();
        $remainingQuota = $maxAttachments - $existingAttachmentsCount;
        
        if ($remainingQuota <= 0) {
            return response()->json([
                'message' => 'Hai raggiunto il limite massimo di ' . $maxAttachments . ' immagini.'
            ], 422); // 'You have reached the maximum limit of {n} images.'
        }
    
        // Phase 1: Basic validation
        $validator = Validator::make($request->all(), [
            'images' => 'required|array|max:'.min($maxBatchSize, $remainingQuota),
            'set_profile_picture' => 'nullable|boolean'
        ], [
            'images.max' => 'Puoi caricare un massimo di :max immagini in questo momento.', // 'You can upload maximum :max images at this time.'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
    
        $successfulUploads = [];
        $failedUploads = [];
        $imageService = new ImageService();
    
        // Phase 2: Process each image individually
        foreach ($request->file('images') as $file) {
            try {
                // Validate individual image
                $imageValidator = Validator::make(
                    ['image' => $file],
                    [
                        'image' => 'required|image|mimes:jpeg,png,jpg|max:10000',
                    ],
                    [
                        'image.image' => 'Il file deve essere un\'immagine.', // 'The file must be an image.'
                        'image.mimes' => 'Sono consentite solo immagini jpeg, png e jpg.', // 'Only jpeg, png and jpg images are allowed.'
                        'image.max' => 'L\'immagine non deve superare i 10MB.', // 'The image must not be larger than 10MB.'
                    ]                    
                );
    
                if ($imageValidator->fails()) {
                    $failedUploads[] = [
                        'name' => $file->getClientOriginalName(),
                        'errors' => $imageValidator->errors()->all()
                    ];
                    continue;
                }
    
                // Process valid image
                $path = $imageService->processAndSaveImage(
                    $file,
                    'attachments/' . $user->id,
                    true
                );
    
                $attachment = Attachment::create([
                    'user_id' => $user->id,
                    'path' => $path,
                ]);
    
                $successfulUploads[] = $attachment;
    
            } catch (\Exception $e) {
                $failedUploads[] = [
                    'name' => $file->getClientOriginalName(),
                    'errors' => [$e->getMessage()]
                ];
            }
        }
    
        $PFP = false;
        // Handle profile picture setting if requested
        if ($request->set_profile_picture && count($successfulUploads)) {
            $user->update(['profile_picture_id' => $successfulUploads[0]->id]);
            $PFP = true;
        }
    
        // Prepare response
        $response = [
            'message' => count($successfulUploads) ? 'Caricamento completato' : 'Nessuna immagine è stata caricata', // 'Upload completed' / 'No images were uploaded'
            'uploaded_images' => array_map(function($attachment) {
                return [
                    'id' => $attachment->id,
                    'url' => route('attachments.show', $attachment->id),
                ];
            }, $successfulUploads),
            'failed_uploads' => $failedUploads,
            'remaining_quota' => $maxAttachments - ($existingAttachmentsCount + count($successfulUploads)),
            'set_profile_picture' => $PFP,
        ];
    
        return response()->json(
            $response,
            count($successfulUploads) ? 200 : 422
        );
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $attachment = Attachment::where('user_id', $user->id)->findOrFail($id);
        
        Storage::disk('local')->delete($attachment->path);
        $attachment->delete();

        return response()->json(['message' => 'Immagine eliminata con successo']); // 'Image deleted successfully'
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

        return response()->json(['message' => 'Immagine del profilo aggiornata con successo']); // 'Profile picture updated successfully'
    }
}