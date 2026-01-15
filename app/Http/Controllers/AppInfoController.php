<?php

namespace App\Http\Controllers;

use App\Models\AppInfo;
use Illuminate\Http\Request;

class AppInfoController extends Controller
{
    public function index()
    {
        $appInfo = AppInfo::first();
        $hideSearch = true;
        return view('admin.app-info.index', compact('appInfo', 'hideSearch'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'text' => 'required|string',
            'note' => 'required|string',
            'instagram' => 'nullable|url',
            'facebook' => 'nullable|url',
            'whatsapp' => 'nullable|string',

        ]);

        AppInfo::updateOrCreate(
            ['id' => 1],
            $data
        );

        return redirect()->back()->with('success', 'تم حفظ المعلومات بنجاح');
    }
}
