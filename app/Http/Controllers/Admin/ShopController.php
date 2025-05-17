<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\_Shop;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
     public function index(Request $request)
    {
       $query = Shop::with('user')->orderBy('created_at', 'desc');

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                    $userQuery->where('name', 'like', "%{$searchTerm}%");
                });
            });
        }
        
        $shops = $query->paginate(10);
        return view('shops.index', compact('shops'));
    }

    public function create()
    {
        return view('shops.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:100',
            'price'    => 'required',
            'credits'  => 'required|integer',
            'icon'     => 'nullable',

        ]);

        $shop = new Shop();

        $shop->title  = $request->title;
        $shop->price = $request->price;
        $shop->credits = $request->credits;
        $shop->user_id = Auth::user()->id ?? null;

        if ($request->hasFile('icon')) {
            $file = $request->file('icon');

            $filename = time() . '_' . mt_rand(100000, 999999) . '.' . $file->getClientOriginalExtension();

            $destinationPath = public_path('shops');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $shop->icon = $filename;
        }


    $shop->save();

        return redirect()->route('shops.index')->with('success', 'Shop created successfully.');
    }

    public function edit($id)
    {
        $shop = Shop::findOrFail($id);
        return view('shops.edit', compact('shop'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:100',
            'price'    => 'required',
            'credits'  => 'required|integer',
            'icon'     => 'nullable',

        ]);

        $shop = Shop::where('id',$request->id)->first();

        $shop->title  = $request->title;
        $shop->price = $request->price;
        $shop->credits = $request->credits;
        $shop->user_id = Auth::user()->id ?? null;

        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            $filename = time() . '_' . mt_rand(100000, 999999) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('shops');
         
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            $shop->icon = $filename;
        }


        $shop->save();
        return redirect()->route('shops.index')->with('success', 'Shop updated successfully.');
    }

    public function destroy(Request $request)
    {
        $shop = Shop::where('id',$request->id)->first();
        $shop->delete();

        return redirect()->route('shops.index')->with('success', 'Shop deleted successfully.');
    }
}
