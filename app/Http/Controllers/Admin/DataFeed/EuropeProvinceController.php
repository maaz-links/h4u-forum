<?php

namespace App\Http\Controllers\Admin\DataFeed;

use App\Http\Controllers\Controller;
use App\Models\EuropeCountry;
use App\Models\EuropeProvince;
use App\Models\UserProfile;
use Illuminate\Http\Request;

class EuropeProvinceController extends Controller
{
    public function index()
    {
        // $europe_provinces = EuropeProvince::with('country')->get()
        // ->sortBy(function ($province) {
        //     return $province->country->name ?? ''; // null-safes
        // });
        $europe_provinces = EuropeProvince::with('country')
        ->join('europe_countries', 'europe_provinces.country_id', '=', 'europe_countries.id')
        ->orderBy('europe_countries.name')
        ->orderBy('europe_provinces.name')
        ->select('europe_provinces.*') // avoid column collision
        ->get();
        // return $europe_provinces;
        return view('europe-provinces.index', compact('europe_provinces'));
    }

    public function create()
    {
        $countries = EuropeCountry::orderBy('name')->get();
        return view('europe-provinces.form', compact('countries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:europe_countries,id',
            'name' => 'required|string|max:255',
        ]);

        //EuropeProvince::create($validated);
        $this->createProvince($request->name,$request->country_id);

        return redirect()->route('europe-provinces.index')
            ->with('success', 'Province created successfully');
    }

    public static function createProvince($name, $country_id){
        EuropeProvince::create([
            'country_id' => $country_id,
            'name' => $name,
        ]);
    }

    public function edit(EuropeProvince $europe_province)
    {
        $countries = EuropeCountry::orderBy('name')->get();
        return view('europe-provinces.form', compact('europe_province', 'countries'));
    }

    public function update(Request $request, EuropeProvince $europe_province)
    {
        $validated = $request->validate([
            // 'country_id' => 'required|exists:europe_countries,id',
            'name' => 'required|string|max:255',
        ]);

        $europe_province->update($validated);

        return redirect()->route('europe-provinces.index')
            ->with('success', 'Province updated successfully');
    }

    public function destroy(EuropeProvince $europe_province)
    {
        $country = $europe_province->country;

        if ($country && $country->provinces()->count() <= 1) {
            return redirect()->route('europe-provinces.index')
                ->with('error', 'Cannot delete the only province of '.$country->name);
        }
        
        $europe_province->delete();

        $countryFirstProvinceId = $country->provinces->first()->id;
        UserProfile::whereNull('province_id')->update([
            'country_id'  => $country->id,
            'province_id' => $countryFirstProvinceId,
        ]);

        return redirect()->route('europe-provinces.index')
            ->with('success', 'Province deleted successfully');
    }

}
