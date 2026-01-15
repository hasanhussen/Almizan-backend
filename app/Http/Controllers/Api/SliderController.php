<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slider;

class SliderController extends Controller
{
    public function getSlider()
    {
        $sliderImages = Slider::pluck('image')->toArray();
        return response()->json(['sliders' => $sliderImages]);
    }
}
