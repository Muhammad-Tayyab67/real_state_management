<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\{Validator, Storage};

use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    //get all users
    public function list()
    {
        $users = User::all();
        return response()->json([
            'success' => true,
            'users' => $users,
        ]);
    }

    //get single user
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ]);
        }
        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }

    //update user
    public function update(Request $request, $id)
    {
        // Validating request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'userType' => 'required|string|max:255',
            //phone number validation 11 integer
            'phoneNumber' => 'required|string|max:255',
            'profilePicture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // returning error message on validation fail
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        // finding user
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ]);
        }

        //profile picture
        if ($request->hasFile('profilePicture')) {

            if ($user->profilePicture) {
                //Deleting existing image
                $existingImagePath = basename($user->profilePicture);

                Storage::delete('public/profilePictures/'. $user->id. '/' . $existingImagePath);
            }
            //Storing image and returning its url to diplays in frontend react app
            $path = $request->file('profilePicture')->store('public/profilePictures/'.$user->id.'/');

            //get img src url
            $url = asset('storage/profilePictures/' . $user->id . '/' . basename($path));

            //updating user
            $user->update([
                'profilePicture' => $url,
            ]);
        }

        // updating user
        $user->update([
            'name' => $request->name,
            'userType' => $request->userType,
        ]);

        // returning response
        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }

    //delete user
    public function delete($id)
    {
        // finding user
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ]);
        }

        //Deleting existing image
        if ($user->profilePicture) {
            $existingImagePath = basename($user->profilePicture);

            Storage::delete('public/profilePictures/'. $user->id. '/' . $existingImagePath);
        }

        // deleting user
        $user->delete();

        // returning response
        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }

    //get plots of user from share table
    public function plots()
    {
        $user = auth()->user();

        $plots = $user->plots;

        return response()->json([
            'success' => true,
            'plots' => $plots,
        ]);
    }
}
