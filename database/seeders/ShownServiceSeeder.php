<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use App\Models\ShownService;

class ShownServiceSeeder extends Seeder
{
    public function run(): void
    {
         // ─── 0. Clean existing records and files ─────────────────────────────────
        $existing = ShownService::all();

        foreach ($existing as $record) {
            if ($record->image_path && Storage::disk('public')->exists($record->image_path)) {
                Storage::disk('public')->delete($record->image_path);
            }
        }
        // Truncate table (reset auto-increment, optional)
        ShownService::truncate();
        // ─── 1.  Describe your seed data ──────────────────────────────────────────
        $records = [
            [
                'name'        => 'Hostess',
                'description' => 'Attend events, dinners, or private gatherings with a polished, professional presence.',
                'file'        => 's-hostess.png',
            ],
            [
                'name'        => 'Wing Woman',
                'description' => 'Navigate social environments with confidence and support; impress at business mixers or casual parties.',
                'file'        => 's-wingwoman.png',
            ],
            [
                'name'        => 'Sugar Baby',
                'description' => 'Engage in mutually beneficial relationships with clarity, honesty, and discretion.',
                'file'        => 's-sugarbaby.png',
            ],
        ];

        // ─── 2.  Where the source images live now ─────────────────────────────────
        // e.g. database/seeders/shown-services-seeder/servic1.png
        $sourceDir = database_path('seeders/shown-services-seeder');

        // ─── 3.  Loop, copy, and create DB rows ───────────────────────────────────
        foreach ($records as $item) {

            $srcPath   = $sourceDir.'/'.$item['file']; // full path to image in repo
            $file      = new File($srcPath);

            /*  This call is identical to what happens in your controller:

                    $request->file('image')->store('shown-services', 'public');

                It copies the file into storage/app/public/shown-services
                and returns the _relative_ path, e.g. "shown-services/servic3.png".
            */
            $imagePath = Storage::disk('public')
                                ->putFileAs('shown-services', $file, $item['file']);

            // finally insert the row
            ShownService::create([
                'name'        => $item['name'],
                'description' => $item['description'],
                'image_path'  => $imagePath,   // identical to controller behaviour
            ]);
        }
    }
}
