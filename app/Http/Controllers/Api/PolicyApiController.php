<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Policy;

class PolicyApiController extends Controller
{

    public function index()
    {
        $appInfo = Policy::first();

        return response()->json([
            'title' => optional($appInfo)->title,
            'body' => optional($appInfo)->body,
        ]);
    }
}
