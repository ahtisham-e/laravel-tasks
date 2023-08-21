<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        'first_name' => 'required|string',
        'last_name' => 'required|string',
        'email' => 'required|email|unique:users',
        'phone' => 'required|unique:users',
        'password' => 'required|min:6|confirmed',
    ];
    
    // Validate the request data
    $validator = Validator::make($request->all(), $rules);
    
    // Check if validation fails
    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }
    if(User::where('phone','=',$request->phone)->orWhere('email','=',$request->email)->first()){
         $response = [
            'status' => 200,
            'data' => [],
            'message' => 'User already registere',
    
         ];
    }else{
       
       
            $user =new User;
            $user->first_name = $request->first_name;
            $user->last_name = $request->input('last_name');
          //  'name' =>  $request->input('first_name').' '.$request->input('last_name'),
           $user->email = $request->input('email');
           $user->phone = $request->input('phone');
           $user->password = Hash::make($request->input('password'));
        
        
        if($user->save()){
            $response = [
                'status' => 200,
                'data' => $user,
                'message' => 'User registere successfully',
        
             ];
        }else{
            $response = [
                'status' => 200,
                'data' => [],
                'message' => 'something went wrong',
        
             ]; 
        }
     
    }
      
    
        return response()
        ->json($response);
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
