<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        //get users
        $users = User::when(request()->search, function ($users) {
            $users = $users->where('name', 'like', '%' . request()->search . '%');
        })->with('roles')->latest()->paginate(5);

        //append query string to pagination links
        $users->appends(['search' => request()->search]);

        //return with Api Resource
        return new UserResource(true, 'List Data Users', $users);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users|email',
            'password' => 'required|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $user->assignRole($request->roles);
        if ($user) {
            return new UserResource(true, 'Data User Berhasil Disimpan!', $user);
        }

        return new UserResource(false, 'Data User Gagal Disimpan!', null);
    }

    public function show($id)
    {
        $user = User::with('roles')->whereId($id)->first();

        if ($user) {
            return new UserResource(true, 'Details Data User', $user);
        }

        return new UserResource(false, 'Details Data User Tidak Ditemukan', null);
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $user->id,
            'password' => 'confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->password == "") {
            $user->update([
                'name' => $request->name,
                'email' => $request->email
            ]);
        } else {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);
        }

        $user->syncRoles($request->roles);

        if ($user) {
            return new UserResource(true, 'Data User Berhasil Diupdate!', $user);
        }

        return new UserResource(false, 'Data User Gagal Diupdate!', null);
    }

    public function destroy(User $user)
    {
        if ($user->delete()) {
            return new UserResource(true, 'Data User Berhasil Dihapus!', $user);
        }

        return new UserResource(false, 'Data User Gagal Dihapus!', null);
    }
}
