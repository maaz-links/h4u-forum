<?php

namespace App\Http\Controllers\Admin\DataFeed;

use App\Http\Controllers\Controller;
use App\Models\EuropeCountry;
use App\Models\UserProfile;
use DB;
use Illuminate\Http\Request;

class EuropeCountryController extends Controller
{
    public function index()
    {
        $europe_countries = EuropeCountry::orderBy('name')->get();
        return view('europe-countries.index', compact('europe_countries'));
    }

    public function create()
    {
        return view('europe-countries.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:europe_countries,name',
            'province_name' =>  'required|string|max:255',
        ]);

        $country = EuropeCountry::create($validated);
        EuropeProvinceController::createProvince($request->province_name,$country->id);

        return redirect()->route('europe-countries.index')
            ->with('success', 'Country created successfully');
    }

    public function edit(EuropeCountry $europe_country)
    {
        return view('europe-countries.form', compact('europe_country'));
    }

    public function update(Request $request, EuropeCountry $europe_country)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:europe_countries,name,'.$europe_country->id,
        ]);

        $europe_country->update($validated);

        return redirect()->route('europe-countries.index')
            ->with('success', 'Country updated successfully');
    }

    public function destroy(EuropeCountry $europe_country)
    {
        $totalCountries = EuropeCountry::count();
        if ($totalCountries <= 1) {
            return redirect()->route('europe-countries.index')
                ->with('error', 'Cannot delete the only remaining country.');
        }
        
        if($europe_country->isDefault()){
            return redirect()->route('europe-countries.index')
            ->with('error', 'Cannot delete the default country');
        }


        $europe_country->delete();

        //Change Update Logic of Users
        $this->replaceWithDefaultValues();

        return redirect()->route('europe-countries.index')
            ->with('success', 'Country deleted successfully');
    }

    public function setDefault(EuropeCountry $europe_country){
        
        $this->putDefaultInDatabase($europe_country);

        return redirect()->route('europe-countries.index')
            ->with('success', $europe_country->name.' is now default country.');
    }

    protected function putDefaultInDatabase(EuropeCountry $europe_country){
        if ($europe_country->provinces()->exists()) {
            DB::transaction(function () use ($europe_country) {
                EuropeCountry::where('is_default', true)->update(['is_default' => false]);
                $europe_country->update(['is_default' => true]);
            });
        }
    }

    protected function replaceWithDefaultValues(){

        $defaultCountry = EuropeCountry::with('provinces')->where('is_default', true)->first();

        if ($defaultCountry && $defaultCountry->provinces->isNotEmpty()) {
            $defaultProvinceId = $defaultCountry->provinces->first()->id;

            UserProfile::whereNull('country_id')->update([
                'country_id'  => $defaultCountry->id,
                'province_id' => $defaultProvinceId,
            ]);
        }
    }

    public static function getDefaultCountryValues(){
        $country = EuropeCountry::with('provinces')->where('is_default', true)->first();

        return [
            'country_id' => $country?->id,
            'province_id' => $country?->provinces->isNotEmpty()
                ? $country->provinces->random()->id
                : null,
        ];
    }
}
