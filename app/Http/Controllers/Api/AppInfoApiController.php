<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppInfo;

class AppInfoApiController extends Controller
{
    public function index()
    {
        $appInfo = AppInfo::first();

        return response()->json([
            'text' => optional($appInfo)->text,
            'note' => optional($appInfo)->note,
            'instagram' => optional($appInfo)->instagram,
            'facebook' => optional($appInfo)->facebook,
        ]);
    }
}
