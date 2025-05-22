<?php

namespace App\Listeners;

use App\Events\GenerateFakeProfiles;
use App\Models\Attachment;
use App\Models\EuropeProvince;
use App\Models\HostessService;
use App\Models\Interest;
use App\Models\SpokenLanguage;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use DB;
use File;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Hash;
use Log;
use Storage;
use Str;
use Faker\Factory as Faker;

class GenerateFakeProfilesListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(GenerateFakeProfiles $event)
    {
        //Get CSV file
        $path = 'fakedata/fakeprofile.csv';
        
        if (!Storage::disk('local')->exists($path)) {
                throw new \Exception("CSV file not found: $path");
        }
        
        $csv = Storage::disk('local')->get($path);
        
        // Convert CSV string to array of rows
        $lines = explode(PHP_EOL, $csv);
        $header = str_getcsv(array_shift($lines), ";"); // assumes tab-delimited

        // Parse rows
        $rows = array_filter(array_map(function ($line) use ($header) {
            $values = str_getcsv($line, ";");
            return count($values) === count($header) ? array_combine($header, $values) : null;
        }, $lines));

        //Age Criteria
        $minAge = $event->given_data['min_age'];
        $maxAge = $event->given_data['max_age'];

        // Filter rows by Età and Città
        $filtered = collect($rows)->filter(function ($row) use ($minAge, $maxAge) {
            // Clean and cast Età (make sure it's a number)
            $eta = isset($row['Età']) ? (int) filter_var($row['Età'], FILTER_SANITIZE_NUMBER_INT) : null;

            return $eta !== null
                && $eta >= $minAge
                && $eta <= $maxAge
                ;
                //&& isset($row['Città'])
                //&& strtolower(trim($row['Città'])) === strtolower(trim($city));
        });

        $filteredProfiles = $filtered->values(); // ->toArray() if needed

       
        //dd($randomProfile);
        $MALE = "male";
        $FEMALE = "female";
        $count = $event->given_data['profile_count'];
        $eyeColors = ['amber', 'blue', 'brown', 'gray', 'green', 'hazel'];
        $profiles = [];

        $availableFor = HostessService::all();
        $personalInterests = Interest::all();
        $availableLanguages = SpokenLanguage::all();
        $languagesMapped = [
            'Inglese'   => 'English',
            'Spagnolo'  => 'Spanish',
            'Italiano'  => 'Italian',
            'Francese'  => 'French',
        ];
        
        $faker = Faker::create();
        for ($i = 0; $i < $count; $i++) {

            //If filtered values are empty, pick randomly from any row outside of filter.
            if(count($filteredProfiles) > 0) {
                $randomProfile = $filteredProfiles->random();
            }else{
                $randomProfile = collect($rows)->random();
            }

            //MAKE SURE USERNAME IS UNIQUE
            $baseName = trim($randomProfile['Username']);
            // Initialize final name variable
            $uniqueName = $baseName;
            // Check if it exists in users table
            if (DB::table('users')->where('name', $baseName)->exists()) {
                do {
                    // Append random 3-digit string
                    $suffix = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
                    $uniqueName = $baseName . $suffix;
                } while (DB::table('users')->where('name', $uniqueName)->exists());
            }
            //dd($uniqueName,$randomProfile);


            $contactString = $randomProfile['Contatti'];
            
            // Split into parts by pipe
            $parts = explode('|', $contactString);
            $telegram = null;
            $email = null;
            foreach ($parts as $part) {
                $part = trim($part);
                if (str_starts_with($part, 'Telegram:')) {
                    $telegram = trim(str_replace('Telegram:', '', $part));
                }
                if (str_starts_with($part, 'Email:')) {
                    $email = trim(str_replace('Email:', '', $part));
                }
            }

            //MAKE SURE EMAIL IS UNIQUE
            $baseEmail = trim($email); // e.g., salveminifredo@navone.it
            $uniqueEmail = $baseEmail;

            if (DB::table('users')->where('email', $baseEmail)->exists()) {
                // Split email into name and domain parts
                [$name, $domain] = explode('@', $baseEmail, 2);

                do {
                    // Generate random 5-digit suffix
                    $suffix = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
                    $uniqueEmail = $name . $suffix . '@' . $domain;
                } while (DB::table('users')->where('email', $uniqueEmail)->exists());
            }

            // $gender = $event->given_data['gender'] === 'both' 
            //     ? (rand(0, 1) ? 'male' : 'female')
            //     : $event->given_data['gender'];

            $gender = 'female';

            // $username = strtolower(Str::slug($name)) . rand(100, 999);

            // Generate random age within requested range
            //$age = random_int($event->given_data['min_age'], $event->given_data['max_age']);
            $age = $randomProfile['Età'];
            $dob = now()->subYears($age)->format('Y-m-d');

            $role = $gender === $MALE 
                ? User::ROLE_KING
                : User::ROLE_HOSTESS;

            //Find Province
            $myProvince =
            //EuropeProvince::with('country')->where('id', $event->given_data['province'])->first();
            EuropeProvince::with('country')->where('name', $randomProfile['Città'])->first();
            if(!$myProvince){
                $myProvince = EuropeProvince::with(['country' => function($query){
                    $query->where('name', 'Italy')->first();
                }])->inRandomOrder()->first();
            }
            //$firstCountryId = $firstProvinceId->country->id ?? null;
            $height = $randomProfile['Altezza'];
            $weight = $randomProfile['Peso'];
            // $height = $gender === $MALE 
            //     ? random_int(165, 195)  // Male height range in cm
            //     : random_int(150, 180); // Female height range in cm
            
            // $weight = $gender === $MALE
            //     ? random_int(65, 95)    // Male weight range in kg
            //     : random_int(45, 75);   // Female weight range in kg

            $top_profile = random_int(0,1);
            $verified_profile = random_int(0,1);
            
            // Generate European sizes based on gender
            $dressSize = $gender === $MALE
                ? 'M'   // Male dress size (European)
                : 'S';  // Female dress size (European)
            
            $shoeSize = $gender === $MALE
                ? random_int(39, 46)   // Male shoe size (European)
                : random_int(36, 42);  // Female shoe size (European)

            $password = str()->random(16);
            $user = new User([
                'name' => $uniqueName,
                'email' => $uniqueEmail,
                'password' => Hash::make($password),
                'phone' => "+391234567890",
                'dob' => $dob,
                'role' => $role,
                'dummy_id' => $event->script->id,
            ]);
           
            //return response()->json($user);
            $user->save();

            $description = $randomProfile['Descrizione'];

            $mainAttrib = [
                'user_id' => $user->id,
                'nationality' => 'Italian',
                'description' => $description,
                'shoe_size' => $shoeSize,
                'height' => $height,
                'country_id' => $myProvince->country->id ?? null,
                'province_id' => $myProvince->id,
                'eye_color' => $eyeColors[array_rand($eyeColors)],
                'top_profile' => $top_profile,
                'verified_profile' => $verified_profile,
            ];

            $femaleAttrib = $gender === $MALE
                ?   []:
                [
                    'shoe_size' => $shoeSize,
                    'dress_size' => $dressSize,
                    'weight' => $weight,
                    'telegram' => $telegram,
                ];
            
            $profile = new UserProfile($mainAttrib + $femaleAttrib);

            $profile->save();

            $user->update(['profile_picture_id' => $this->assignRandomProfilePicture($user->id,$gender)]);
            

            //$RegistrationDate = $randomProfile['Data Registrazione'];
            $rawDate = trim($randomProfile['Data Registrazione']);
            // Try parsing using Carbon (adjust format if needed)
            $createdAt = Carbon::parse($rawDate)->format('Y-m-d H:i:s');
            DB::table('users')
            ->where('id', $user->id)
            ->update(['created_at' => $createdAt]);

            if($gender !== $MALE){
                $randomServices = $availableFor->random(rand(3, 5))->pluck('id')->toArray();
                $profile->hostess_services()->sync($randomServices);
                $randomHobbies = $personalInterests->random(rand(3, 6))->pluck('id')->toArray();
                $profile->interests()->sync($randomHobbies);
            }

            $myLanguage = $languagesMapped[$randomProfile['Lingue']];
            if(!$myLanguage){
                $myLanguage = "English";
            }
            $finalLanguage = $availableLanguages->firstWhere('name', $myLanguage)?->id;
            // $randomLanguages = $availableLanguages->random(rand(2, 3))->pluck('id')->toArray();
            // $profile->spoken_languages()->sync($randomLanguages);

            $profile->spoken_languages()->sync([$finalLanguage]);
            
        }

        //Set emails as verified
        DB::table('users')
        ->where('dummy_id', $event->script->id)
        ->update(['email_verified_at' => now()]);

    }

    private function assignRandomProfilePicture($userId, $gender = 'female')
    {
        $disk = Storage::build([
            'driver' => 'local',
            'root' => storage_path('app/private'),
        ]);
        
        $sourcePath = "/pictureRepo/{$gender}/";
        $files = $disk->files($sourcePath);
        //dd($disk,$files);
        
        if (empty($files)) {
            throw new \Exception("No images found");
        }
        
        $randomFile = $files[array_rand($files)];
        $extension = pathinfo($randomFile, PATHINFO_EXTENSION);
        //$newFilename = "attachments/{$userId}/profile_".time().".{$extension}";
        

            $newFilename = 'attachments/' . $userId . '/' . Str::uuid()->toString() . '.webp';
            $fullPath = storage_path('app/private/' . $newFilename);

            // Ensure the directory exists
            $directory = dirname($fullPath);
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

        $disk->copy($randomFile, $newFilename);

        $attachment = Attachment::create([
            'user_id' => $userId,
            'path' => $newFilename,
        ]);
        
        return $attachment->id;
    }

    protected function generateEuropeanPhone($countryCode = null)
    {
        // if(!$countryCode){
        //     $countries = ['DE', 'FR', 'IT', 'ES', 'GB', 'NL', 'BE', 'SE', 'CH', 'AT'];
        //     $country = $countries[array_rand($countries)];
        // }else{
        //     $country = $countryCode;
        // }
        
        // $phoneUtil = PhoneNumberUtil::getInstance();
        // $example = $phoneUtil->getExampleNumber($country);
        // $randomized = $phoneUtil->parse($example, $country);
        // dd($randomized);
        // // Randomize the last digits
        // $nationalNumber = substr($example->getNationalNumber(), 0, 3) . rand(100000, 999999);
      //dd($nationalNumber);

        // $phoneNumber = new PhoneNumber("+391234567890");
        // $phoneNumber = $phoneNumber->formatE164();
        // return $phoneNumber;

        // return PhoneNumber::make($nationalNumber, $country)
        //     ->format(PhoneNumberFormat::E164);
    }
}
