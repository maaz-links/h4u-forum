<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImageService
{
    protected $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Process and save an uploaded image
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $storagePath Path of file to be stored, either in private or public (Example: attachments/uploads/)
     * @param int $width Set Compressed Width 
     * @param int $height Set Compressed Height
     * @param string $format Set Format
     * @param bool $InPrivate Set to true (default) if you want to store in private folder, otherwise store in public folder by setting to false
     * @return string Path to the saved image
     */
    public function processAndSaveImage(
        $file,
        $storagePath,
        bool $InPrivate = true,
        $width = 1000,
        $height = 1000,
        $format = 'webp'
    ) {
        // Read the image
        $image = $this->manager->read($file->getRealPath());
        
        // Scale the image while maintaining aspect ratio
        $image->scale($width, $height);
        
        // Determine the full path
        $filename = Str::uuid()->toString() . '.' . $format;
        $relativePath = trim($storagePath, '/') . '/' . $filename;
        
        $fullPath = $InPrivate ? storage_path('app/private/' . $relativePath) : storage_path('app/public/' . $relativePath);

        // Ensure directory exists
        $this->ensureDirectoryExists(dirname($fullPath));

        // Save the image in the specified format
        match ($format) {
            'webp' => $image->toWebp()->save($fullPath),
            'jpg', 'jpeg' => $image->toJpeg()->save($fullPath),
            'png' => $image->toPng()->save($fullPath),
            default => $image->toWebp()->save($fullPath),
        };

        return $relativePath;
    }

    /**
     * Ensure a directory exists, create it if not
     *
     * @param string $directory
     */
    protected function ensureDirectoryExists($directory)
    {
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
    }
}