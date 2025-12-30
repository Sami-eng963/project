<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash as FacadesHash;
use Illuminate\Support\Facades\Log;


class userController extends Controller
{
   



public function register(Request $request){
    $request->validate([
        'first_name'=>'required|string|max:20|min:3',
        'last_name'=>'required|string|max:20|min:3',
        'phone'=>'required|string',
        'password'=>'required|string|confirmed|min:8',
        'profile_image'=>'required|image|mimes:png,jpg,jpeg,gif',
        'ID_card_image'=>'required|image|mimes:png,jpg,jpeg,gif',
        'date_of_birth'=>'required|string'
    ]);

    if (User::where('phone', $request->phone)->exists()) {
        return response()->json([
            'message' => 'The phone already exists'
        ], 404);
    }

    $path1 = null;
    $path  = null;

    if($request->hasFile('ID_card_image')){
        $path1 = $request->file('ID_card_image')->store('images','public');
        
    }

    if($request->hasFile('profile_image')){
        $path = $request->file('profile_image')->store('images','public');
        
    }

    $user = User::create([
        'first_name' => $request->first_name,
        'last_name'  => $request->last_name,
        'phone'      => $request->phone,
        'password'   => FacadesHash::make($request->password),
        'profile_image' => $path=asset(path:'storage/'.$path),
        'ID_card_image' => $path1=asset(path:'storage/'.$path1),
        'date_of_birth' => $request->date_of_birth
    ]);

    return response()->json([
        'message' => 'User registered successfully',
        'user' => $user
    ], 201);
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function login(Request $request){
        $request->validate(['phone'=>'required|string',
                            'password'=>'required|string']);
        if(!Auth::attempt($request->only('phone','password')))
            return response()->json(['message'=>'invalid phone or password 12'],401);

        $user=User::where('phone',$request->phone)->firstOrFail();
  
        $token=$user->createToken('auth_token')->plainTextToken;
              Log::debug($token);
        return response()->json(['message'=>'login successfuly','user'=>$user,'token'=>$token],201) ; 
    }//////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message'=>'logout successfuly'],204);
        
    }
}
