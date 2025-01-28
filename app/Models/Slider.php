<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slider extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'image',
    ];

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn($image) => url('/storage/sliders/' . $image),
        );
    }
}
