<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    // Store or Register New User.
    public function store(Request $request)
    {
        //Validate the client data
        $validate = Validator::make($request->all(),[
            'name' => 'required|min:4',
            'email' => 'required|email|string|unique:users,email',
            'password' => 'required'
        ])->validate();
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $validate['name'],
                'email' => $validate['email'],
                'password' => bcrypt($validate['password'])
            ]);
            DB::commit();
            // return response from the server.
            return response()->json([
                "status" => true,
                "message" => "Thanks for registering.",
                "data" => [$user]
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                "status" => false,
                "message" => "Something went wrong!",
                "data" => []
            ]);
        }
    }

    // Login New User.
    public function login(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required'
        ])->validate();

        DB::beginTransaction();
        try {
            $credentials = $request->only('email', 'password');

            // If the user credintials does not match then this shows the error message other wise user is logged in
            if (!Auth::attempt($credentials)) {
                return response()->json([
                    "status" => false,
                    "message" => "Login failure!",
                    "data" => [
                        "email" => 'records do not matched',
                        "password" => 'records do not matched'
                    ]
                ]);
            }
            $token = Auth::user()->createToken('login_token')->plainTextToken;
            DB::commit();
            return response()->json([
                "status" => true,
                "message" => "Login successfully!",
                "token" => $token,
                "data" => []
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                "status" => false,
                "message" => "Something went wrong!",
                "data" => []
            ]);
        }
    }

    // Getting The User Profile
    public function profile()
    {
        $user = Auth::user();
        return response()->json([
            "status" => 200,
            "message" => "User Profile Data",
            "data" => [
               $user
            ]
        ]);
    }

    // Logout The User
    public function logout()
    {
        try {
            $user = Auth::user();
            $user->tokens()->delete();
            return response()->json([
                "status" => 201,
                "message" => "user logout success",
                "data" => []
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => "Something went wrong!",
                "data" => []
            ]);            
        }

    }

}
