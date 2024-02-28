<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    /**
     * Display a listing of the permissions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $permissions = Permission::all();
            return response()->json($permissions);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Server error: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Store a newly created permission in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|unique:permissions,name',
            ]);

            // Start a database transaction
            DB::beginTransaction();
            $permission = Permission::create([
                'name' => $request->input('name'),
            ]);

            // Commit transaction
            DB::commit();
            return response()->json($permission, 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction if an exception occurs
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified permission.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $permission = Permission::findOrFail($id);
            return response()->json($permission);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Update the specified permission in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|unique:permissions,name,' . $id,
            ]);

            DB::beginTransaction(); // Start transaction
            $permission = Permission::findOrFail($id);
            $permission->name = $request->input('name');
            $permission->save();

            DB::commit(); // Commit transaction
            return response()->json($permission);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction if an exception occurs
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified permission from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction(); // Start transaction
            $permission = Permission::findOrFail($id);
            $permission->delete();

            DB::commit(); // Commit transaction
            return response()->json(null, 204);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction if an exception occurs
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function assignPermissionsToRole(Request $request)
    {
        // Validate request data
        $request->validate([
            'role_name' => 'required|string|exists:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        try {
            // Find or create the role
            $role = Role::where('name', $request->input('role_name'))->firstOrFail();

            // Attach permissions to the role
            foreach ($request->input('permissions') as $permissionName) {
                $permission = Permission::where('name', $permissionName)->firstOrFail();
                $role->givePermissionTo($permission);
            }

            return response()->json(['message' => 'Permissions assigned to role successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
