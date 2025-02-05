<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SliderResource;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::latest()->paginate(5);

        return new SliderResource(true, 'List Data Sliders', $sliders);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:png,jpg,jpeg|max:2000'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('sliders', $image->hashName());

        $slider = Slider::create([
            'image' => $image->hashName(),
        ]);

        if ($slider) {
            return new SliderResource(true, 'Data Slider Berhasil Disimpan!', $slider);
        }

        return new SliderResource(false, 'Data Slider Gagal Disimpan!', null);
    }

    public function destroy(Slider $slider)
    {

        Storage::disk('public')->delete('sliders' . basename($slider->image));

        if ($slider->delete()) {
            return new SliderResource(true, 'Data Slider Berhasil Dihapus!', null);
        }

        return new SliderResource(false, 'Data Slider Gagal Dihapus!', null);
    }
}
