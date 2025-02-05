<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AparaturResource;
use App\Models\Aparatur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AparaturController extends Controller
{
    public function index()
    {
        $aparaturs = Aparatur::when(request()->search, function ($aparaturs) {
            $aparaturs = $aparaturs->where('name', 'like', '%' . request()->search . '%');
        })->latest()->paginate(5);

        $aparaturs->appends(['search' => request()->search]);

        return new AparaturResource(true, 'List Data Aparaturs', $aparaturs);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|mimes:png,jpg,jpeg|max:2000',
            'name' => 'required',
            'role' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('aparaturs', $image->hashName());

        $aparatur = Aparatur::create([
            'image' => $image->hashName(),
            'name' => $request->name,
            'role' => $request->role,
        ]);

        if ($aparatur) {
            return new AparaturResource(true, 'Data Aparaturs Berhasil Disimpan!', $aparatur);
        }

        return new AparaturResource(false, 'Data Aparaturs Gagal Disimpan!', null);
    }

    public function show($id)
    {
        $aparatur = Aparatur::whereId($id)->first();

        if ($aparatur) {
            return new AparaturResource(true, 'Details Data Aparatur!', $aparatur);
        }

        return new AparaturResource(false, 'Details Data Aparatur Tidak Ditemukan!', null);
    }

    public function update(Request $request, Aparatur $aparatur)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required',
            'role'  => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->file('image')) {
            Storage::disk('public')->delete('aparaturs' . basename($aparatur->image));

            $image = $request->file('image');
            $image->storeAs('aparaturs', $image->hashName());

            $aparatur->update([
                'image' => $image->hashName(),
                'name' => $request->name,
                'role' => $request->role,
            ]);
        }

        $aparatur->update([
            'name' => $request->name,
            'role' => $request->role,
        ]);

        if ($aparatur) {
            return new AparaturResource(true, 'Data Aparatur Berhasil Diupdate!', $aparatur);
        }

        return new AparaturResource(false, 'Data Aparaturs Gagal Diupdate!', $aparatur);
    }

    public function destroy(Aparatur $aparatur)
    {
        Storage::disk('public')->delete('aparaturs' . basename($aparatur->image));

        if ($aparatur->delete()) {
            return new AparaturResource(true, 'Data Aparaturs Berhasil Dihapus!', null);
        }

        return new AparaturResource(false, 'Data Aparaturs Gagal Dihapus!', null);
    }
}
