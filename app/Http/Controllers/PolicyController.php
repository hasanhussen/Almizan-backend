<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    public function index()
    {
        $hideSearch = true;
        $policy = Policy::first();
        return view('admin.policy.index', compact('policy', 'hideSearch'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        Policy::updateOrCreate(
            ['id' => 1],
            $data
        );

        return back()->with('success', 'تم حفظ سياسة الخصوصية');
    }
}
