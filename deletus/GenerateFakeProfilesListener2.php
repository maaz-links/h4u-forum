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
use DB;
use File;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Hash;
use Storage;
use Str;
use Faker\Factory as Faker;

class GenerateFakeProfilesListener2
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
        $hobbies = ['hiking', 'reading', 'cooking', 'traveling', 'movies', 'yoga', 'gaming', 'photography'];
        $traits = ['funny', 'adventurous', 'kind', 'ambitious', 'romantic', 'laid-back', 'outgoing', 'thoughtful','open-minded','open-minded','open-minded'];
        $goals = [
            'looking for a meaningful connection',
            'interested in casual chat',
            'hoping to find my soulmate',
            'wanting to meet new people and see where it goes',
            'looking for someone to share adventures with',
            'hoping for a long-term relationship'
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
            dd($uniqueName,$randomProfile);










            $gender = $event->given_data['gender'] === 'both' 
                ? (rand(0, 1) ? 'male' : 'female')
                : $event->given_data['gender'];

            // $username = strtolower(Str::slug($name)) . rand(100, 999);

            //Make sure that user is unique
            do {
                $uid = random_int(100, 999);
                $name = $faker->name($gender);
                $username = strtolower(Str::slug($name)) . $uid;
            } while (User::where('name', $username)->exists());

            // Generate random age within requested range
            $age = random_int($event->given_data['min_age'], $event->given_data['max_age']);
            $dob = now()->subYears($age)->format('Y-m-d');

            $role = $gender === $MALE 
                ? User::ROLE_KING
                : User::ROLE_HOSTESS;

            //$firstProvinceId = 
            $myProvince =
            EuropeProvince::with('country')->where('id', $event->given_data['province'])->first();
            //$firstCountryId = $firstProvinceId->country->id ?? null;

            $height = $gender === $MALE 
                ? random_int(165, 195)  // Male height range in cm
                : random_int(150, 180); // Female height range in cm
            
            $weight = $gender === $MALE
                ? random_int(65, 95)    // Male weight range in kg
                : random_int(45, 75);   // Female weight range in kg

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
                //'name' => 'fake'.$uid,
                //'email' => 'fake'.$uid.'@email.com',
                'name' => $username,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make($password),
                'phone' => "+391234567890",
                'dob' => $dob,
                'role' => $role,
                'dummy_id' => $event->script->id,
            ]);
           
            //return response()->json($user);
            $user->save();

            $randomHobbies = implode(', ', $faker->randomElements($hobbies, 3));
            $randomTraits = implode(', ', $faker->randomElements($traits, 2));
            $goal = $faker->randomElement($goals);

            $description = "I'm a $randomTraits person. I'm $goal.";

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
                ];
            
            $profile = new UserProfile($mainAttrib + $femaleAttrib);

            $profile->save();

            $user->update(['profile_picture_id' => $this->assignRandomProfilePicture($user->id,$gender)]);
            
            if($gender !== $MALE){
                $randomServices = $availableFor->random(rand(3, 5))->pluck('id')->toArray();
                $profile->hostess_services()->sync($randomServices);
                $randomHobbies = $personalInterests->random(rand(3, 6))->pluck('id')->toArray();
                $profile->interests()->sync($randomHobbies);
            }
            $randomLanguages = $availableLanguages->random(rand(2, 3))->pluck('id')->toArray();
            $profile->spoken_languages()->sync($randomLanguages);
            
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
