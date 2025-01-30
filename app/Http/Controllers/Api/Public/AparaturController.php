<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\AparaturResource;
use App\Models\Aparatur;
use Illuminate\Http\Request;

class AparaturController extends Controller
{
    public function index()
    {
        $aparaturs = Aparatur::oldest()->get();

        return new AparaturResource(true, 'List Data Aparaturs', $aparaturs);
    }
}
