<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $roleUser = Role::where('name','user')->first();
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $roleUser->id,
        ]);
        $token = JWTAuth::fromUser($user);
        $currentUser = User::where('id',$user->id)->with('role')->first();

        return response()->json([
            'message' => 'user berhasil di register',
            'user' => $currentUser,
            'token' => $token
        ], 201);
    }
    
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = Auth::guard('api')->user();
        $currentUser = User::where('id',$user->id)->with('role','profile')->first();
        return response()->json([
            'message' => 'User berhasil Login',
            'user' => $currentUser,
            'token' => $token
        ]);
    }

    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json(['message' => 'Logout berhasil']);
    }

    public function me()
    {
        $userId = Auth::guard('api')->user()->id;
        $user = User::where('id',$userId)->with('role','profile','listBorrow')->first();

        return response()->json([
            'message' =>'Profile berhasil ditampilkan',
            'user' => $user
        ]);
    }

}
