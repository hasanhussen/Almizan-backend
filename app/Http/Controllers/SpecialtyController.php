<?php

namespace App\Http\Controllers;

use App\Models\Specialty;
use App\Http\Requests\SpecialtyRequest;
use Illuminate\Http\Request;

class SpecialtyController extends Controller
{

    public function index(Request $request)
    {
        $query = Specialty::query();



        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('name', 'LIKE', "%$search%");
        }

        $specialties = $query->get();

        return view('admin.specialties.specialties', compact('specialties'));
    }

    // صفحة إضافة صنف
    public function create()
    {
        return view('admin.specialties.create_specialty');
    }

    // حفظ صنف جديد
    public function store(SpecialtyRequest $request)
    {
        // استدعاء التريت
        Specialty::create($request->validated());
        return redirect()->route('specialties.index')->with('success', 'specialty created successfully.');
    }

    // صفحة تعديل صنف
    public function edit(Specialty $specialty)
    {
        return view('admin.specialties.edit_specialty', compact('specialty'));
    }

    // تحديث صنف
    public function update(SpecialtyRequest $request, Specialty $specialty)
    {

        $specialty->update($request->validated());
        return redirect()->route('specialties.index')->with('success', 'specialty updated successfully.');
    }

    // حذف صنف
    public function destroy(Specialty $specialty)
    {
        $specialty->delete();
        return redirect()->route('specialties.index')->with('success', 'specialty deleted successfully.');
    }
}
