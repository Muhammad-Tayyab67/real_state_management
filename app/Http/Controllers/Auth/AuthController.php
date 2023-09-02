<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // validating request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'userType' => 'required|string|max:255',
        ]);

        // returning error message on validation fail
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        // creating user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            // hashing password
            'password' => Hash::make($request->password),
            'userType' => $request->userType,
        ]);

        // generating token
        $token = $user->createToken('auth_token')->plainTextToken;

        // returning response
        return response()->json([
            'success' => true,
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
}
