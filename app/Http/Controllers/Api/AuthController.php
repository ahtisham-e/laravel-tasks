<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    public function signUp(Request $request)
{

   // Define validation rules
   $rules = [
    'name' => 'required|string',
    'email' => 'required|email|unique:users',
    'password' => 'required|min:6|confirmed',
];

// Validate the request data
$validator = Validator::make($request->all(), $rules);

// Check if validation fails
if ($validator->fails()) {
    return response()->json(['errors' => $validator->errors()], 422);
}
    $user = User::create([
        'name' => $request->input('name'),
        'email' => $request->input('email'),
        'password' => Hash::make($request->input('password')),
    ]);

    return response()
    ->json(
        [
            'status' => 200,
            'data' => $user,
            'message' => 'User registered successfully',

        ]
    );
}
public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'status' => 200,
            'data' =>$user,
            'token' => $token
        ]);
    }

    return response()->json(['message' => 'Unauthorized'], 401);
}
}
