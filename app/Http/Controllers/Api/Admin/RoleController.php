<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        //get roles
        $roles = Role::when(request()->search, function ($roles) {
            $roles = $roles->where('name', 'like', '%' . request()->search . '%');
        })->with('permissions')->latest()->paginate(5);

        //append query string to pagination links
        $roles->appends(['search' => request()->search]);

        //return with Api Resource
        return new RoleResource(true, 'List Data Roles', $roles);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'permissions'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create role
        $role = Role::create(['name' => $request->name]);

        //assign permissions to role
        $role->givePermissionTo($request->permissions);

        if ($role) {
            //return success with Api Resource
            return new RoleResource(true, 'Data Role Berhasil Disimpan!', $role);
        }

        //return failed with Api Resource
        return new RoleResource(false, 'Data Role Gagal Disimpan!', null);
    }

    public function show($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        if ($role) {
            return new RoleResource(true, 'Detail Data Role', $role);
        }

        return new RoleResource(false, 'Detail Data Role Tidak Ditemukan!', null);
    }

    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'permissions' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        if ($role) {
            return new RoleResource(true, 'Data Role Berhasil Diupdate', $role);
        }

        return new RoleResource(false, 'Data Role Gagal Diupdate!', null);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if ($role->delete()) {
            return new RoleResource(true, 'Data Role Berhasil Dihapus', $role);
        }

        return new RoleResource(false, 'Data Role Gagal Dihapus!', null);
    }

    public function all()
    {
        $role = Role::latest()->get();

        return new RoleResource(true, 'List Data Roles', $role);
    }
}
