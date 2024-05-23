<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthApiController extends Controller
{
    //
    public function register(Request $request)
    {
        $request->validate([
            'name'=>'required|string',
            'email'=>'required|email|unique:users',
            'password'=>'required|string',
        ]);

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password)
        ]);

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'status' => 200,
            'message' => 'Register successfully.',
            'token' => $token,
            "name" => $user->name

        ]);
    }

    public function login (Request $request){
        $request->validate([
            "email" => "required|email",
            "password" => "required",
        ]);

        $user = User::where("email", $request->email)->first();

        if(!empty($user)){
            if(Hash::check($request->password, $user->password)){
                $token = $user->createToken("my_token")->plainTextToken;
                return response()->json([
                    "status" => 200,
                    "message" => "login successfully",
                    "token" => $token,
                    "id" => $user->id,       
                    "name" => $user->name
                ]);
                
            }else{
                return response()->json([
                    "status" => 401,
                    "message" => "password is wrong"
                ]);
            }
        }else{
            return response()->json([
                "status" => 401,
                "message" => "email is wrong"
            ]);
        }
    }

    public function logout (){
        auth()->user()->tokens()->delete();
        return response()->json([
            "status" => 200,
            "message" => "logout successfully"
        ]);
    }

    public function profile (){
        $user = auth()->user();
        return response()->json([
            "status" => 200,
            "data" => $user,
        ]);
    }


    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleProviderCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Google Authentication Failed'], 500);
        }

        // Check if the user already exists
        $existingUser = User::where('email', $user->email)->first();

        if ($existingUser) {
            // Log in the existing user and generate a token
            $token = $existingUser->createToken('my_token')->plainTextToken;

            return response()->json([
                'status' => 200,
                'message' => 'Login Successful',
                'token' => $token,
                'user' => $existingUser
            ]);
        } else {
            // Create a new user
            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                // You may need to handle other user attributes here
            ]);

            // Log in the newly created user and generate a token
            $token = $newUser->createToken('my_token')->plainTextToken;

            return response()->json([
                'status' => 200,
                'message' => 'New User Created',
                'token' => $token,
                'user' => $newUser
            ]);
        }
    }


}
