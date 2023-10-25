<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

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
            //phone number validation 11 integer
            'phoneNumber' => 'required|string|max:255',
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
            'phoneNumber' => $request->phoneNumber,
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

    public function login(Request $request)
    {
        // Validating request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // returning response on validation fail
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        // finding user by email
        $user = User::where('email', $request->email)->first();

        // returning response if user not found
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Email or Password',
            ]);
        }

        // checking password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Email or Password',
            ]);
        }

        // generating token
        $token = $user->createToken('auth_token')->plainTextToken;

        //logging user
        Auth::guard()->login($user);

        // returning response
        return response()->json([
            'success' => true,
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);

    }

    // logout function
    public function logout(Request $request)
    {
        $user = $request->user();

        if ($token = $request->bearerToken()) {
            $authToken = PersonalAccessToken::findToken($token);
            $authToken->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'user has been logged out!',
        ]);
    }
}
