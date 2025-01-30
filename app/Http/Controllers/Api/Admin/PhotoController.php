<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PhotoResource;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PhotoController extends Controller
{
    public function index()
    {
        $photos = Photo::when(request()->search, function ($photos) {
            $photos = $photos->where('caption', 'like', '%' . request()->search . '%');
        })->latest()->paginate(5);


        $photos->appends(['search' => request()->search]);

        return new PhotoResource(true, 'List Data Photos', $photos);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:png,jpg,jpeg|max:2000',
            'caption' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/photos', $image->hashName());

        $photo = Photo::create([
            'image' => $image->hashName(),
            'caption' => $request->caption
        ]);

        if ($photo) {
            return new PhotoResource(true, 'Data Photo Berhasil Disimpan!', $photo);
        }

        return new PhotoResource(false, 'Data Photo Gagal Disimpan', null);
    }

    public function destroy(Photo $photo)
    {
        Storage::disk('local')->delete('public/photo' . basename($photo->image));

        if ($photo->delete()) {
            return new PhotoResource(true, 'Data Photo Berhasil Dihapus', null);
        }

        return new PhotoResource(false, 'Data Photo Gagal Dihapus', null);
    }
}
