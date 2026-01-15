<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\HasImageUpload;
use App\Models\Slider;
use App\Http\Requests\SliderRequest;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    use HasImageUpload;
    // عرض صفحة الsliderات مع الفلترة
    public function index(Request $request)
    {
        $query = Slider::query();



        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('slider_title', 'LIKE', "%$search%");
        }

        $sliders = $query->get();

        return view('admin.sliders.sliders', compact('sliders'));
    }

    // صفحة إضافة slider
    public function create()
    {
        return view('admin.sliders.create_slider');
    }

    // حفظ slider جديد
    public function store(SliderRequest $request)
    {
        // استدعاء التريت
        $this->handleImageCreation($request, Slider::class, 'sliders');
        return redirect()->route('sliders.index')->with('success', 'slider created successfully.');
    }

    // صفحة تعديل slider
    public function edit(Slider $slider)
    {
        return view('admin.sliders.edit_slider', compact('slider'));
    }

    // تحديث slider
    public function update(SliderRequest $request, Slider $slider)
    {

        $this->handleImageUpdate($request, $slider, 'sliders');
        return redirect()->route('sliders.index')->with('success', 'slider updated successfully.');
    }

    // حذف slider
    public function destroy(slider $slider)
    {
        if ($slider->image) {
            // حذف الصورة من التخزين
            Storage::disk('public')->delete($slider->image);
        }
        $slider->delete();
        return redirect()->route('sliders.index')->with('success', 'slider deleted successfully.');
    }
}
