<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ApartmentController extends Controller
{
    public function storeApartment(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:20|min:1',
            'image'          => 'required|array|min:1|max:6',
            'image.*'        => 'image|mimes:png,jpg,jpeg,gif',
            'space'          => 'required|string',
            'address'        => 'required|string',
            'numberOfRooms'  => 'required|integer',
            'Rent'           => 'required|string',
            'description'    => 'required|string',
            'city'           => 'required|string'
        ]);

        $paths = [];

        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $file) {
                $path = $file->store('images', 'public');
                $paths[] = asset('storage/'.$path);
                
            }
        }

        $apartment = Apartment::create([
    'name'          => $request->name,
    'image'         => $paths,
    'space'         => $request->space,
    'address'       => $request->address,
    'numberOfRooms' => $request->numberOfRooms,
    'Rent'          => $request->Rent,
    'description'   => $request->description,
    'city'          => $request->city,
    'user_id'       => auth()->id(),
  

]);


        return response()->json([
            'message'   => 'added successfully',
            'apartment' => $apartment
        ], 201);
    }

    public function getAllApartment()
    {
        $apartments = Apartment::all();

        

        return response()->json($apartments, 200);
    }

    public function getCustomApartment(Request $request)
    {
        $query = DB::table('apartments');

        if ($request->filled('price')) {
            $query->where('price', $request->price);
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('numberOfRooms')) {
            $query->where('numberOfRooms', $request->numberOfRooms);
        }

        $apartments = $query->get();

        return $apartments;
    }

    public function getDetailsApartment($id)
    {
        $apartment = Apartment::find($id);
  
        return $apartment ;
    }
    public function getUserApartments()
{
    
    $apartments = Apartment::where('user_id', auth()->id())->get();

    return response()->json([
        'message'    => 'Apartments retrieved successfully',
        'apartments' => $apartments
    ], 200);
}



public function deleteApartment($id)
{
    // جلب الشقة
    $apartment = Apartment::findOrFail($id);

    // التحقق إن المستخدم الحالي هو صاحب الشقة
    if ($apartment->user_id !== auth()->id()) {
        return response()->json([
            'error' => 'Unauthorized: You do not own this apartment'
        ], 403);
    }

    // حذف الشقة
    $apartment->delete();

    return response()->json([
        'message' => 'Apartment deleted successfully'
    ], 200);
}

}
