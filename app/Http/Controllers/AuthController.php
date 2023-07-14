<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /* Register New Admin Account */
    public function adminRegister(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:15',
            'email' => 'required|string|unique:users,email',
            'phone' => 'required|string',
            'role' => 'required|string',
            'password' => 'required|string|confirmed'
        ]);

        $old_user = User::where('email', $validated['email'])->first();

        if($old_user){
            return response()->json([
                "message" => "This email has already been taken."
            ]);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password'])
        ]);

        $user["token"] = $user->createToken(time())->plainTextToken;

        return response()->json([
            'user' => $user,
        ], 200);
    }

    /* Register New Customer Account */
    public function customerRegister(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:15',
            'email' => 'required|string|unique:users,email',
            'role' => 'required|string',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password'])
        ]);

        $user["token"] = $user->createToken(time())->plainTextToken;

        return response()->json([
            'user' => $user,
        ], 200);
    }

    /* User Login */
    public function login(Request $request){
        $validated = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
            'role' => 'required'
        ]);

        $user = User::where("email", $validated['email'])->first();

        if(!$user || !Hash::check($validated['password'], $user->password)){
            return response()->json([
                "message" => "The credentials does not match."
            ]);
        }

        if($user->role !== $validated['role']){
            return response()->json([
                "message" => "You are not ".$user->role
            ]);
        }

        $user["token"] = $user->createToken(time())->plainTextToken;

        return response()->json([
            'user' => $user,
        ], 200);
    }

    /* User Logout */
    public function logout(){
        Auth::user()->tokens()->delete();
        return response()->json([
            'message' => 'Logged out'
        ]);
    }
}
