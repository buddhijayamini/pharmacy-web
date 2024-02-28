<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $users = User::all();
            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch users'], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            // Validate request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'role' => 'required|string',
            ]);

            // Start a database transaction
            DB::beginTransaction();
            // Create the user
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['username'],
                'password' => Hash::make($validatedData['password']),
                'role_id' => $validatedData['role'],
            ]);

            // Assign roles to the user
            $user->assignRole($validatedData['role']);
            // Commit the transaction
            DB::commit();

            return response()->json(['message' => 'User registered successfully'], 201);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => 'Server error: ' . $th->getMessage()], 500);
        }
    }

   public function login(Request $request)
    {
        try {
            $credentials = $request->only('username', 'password');

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken('AuthToken')->accessToken;
                return response()->json(['token' => $token], 200);
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } catch (Throwable $th) {
            return response()->json(['error' => 'Server error: ' . $th->getMessage()], 500);
        }
     }

    public function logout(Request $request)
    {
        try {
            $request->user()->token()->revoke();
            return response()->json(['message' => 'Successfully logged out']);
        } catch (Throwable $th) {
            return response()->json(['error' => 'Server error: ' . $th->getMessage()], 500);
        }
    }
}
