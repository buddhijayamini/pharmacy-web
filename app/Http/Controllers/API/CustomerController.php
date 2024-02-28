<?php

namespace App\Http\Controllers\API;

use App\Models\Customer;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $customers = Customer::with('user')->get();
            return response()->json($customers);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch customers'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedUserData = $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8',
                'address' => 'nullable',
                'mobile' => 'nullable',
            ]);

            DB::beginTransaction();
            $user = User::create([
                'name' => $validatedUserData['name'],
                'email' => $validatedUserData['email'],
                'password' => bcrypt($validatedUserData['password']),
                'role' => 3, // Assuming role 3 represents customers
            ]);

            $customer = Customer::create([
                'user_id' => $user->id,
                'name' => $validatedUserData['name'],
                'email' => $validatedUserData['email'],
                'address' => $validatedUserData['address'],
                'mobile' => $validatedUserData['mobile'],
            ]);

            DB::commit();
            return response()->json($customer, 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to create customer'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email,' . $customer->user->id,
                'password' => 'nullable|string|min:8',
                'address' => 'nullable',
                'mobile' => 'nullable',
            ]);

            DB::beginTransaction();
            $customer->user->update($validatedData);

            DB::commit();
            return response()->json($customer, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to update customer'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        try {
            DB::beginTransaction();

            $customer->delete();

            DB::commit();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to soft delete customer'], 500);
        }
    }

    public function forceDelete(Customer $customer)
    {
        try {
            DB::beginTransaction();

            $customer->forceDelete();

            DB::commit();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to permanently delete customer'], 500);
        }
    }
}
