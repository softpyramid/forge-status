<?php

namespace Softpyramid\ForgeStatus\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class ForgeStatusController extends Controller
{
    public function __construct()
    {
        if (config('forge-status.auth_only')) {
            $this->middleware('auth');
        }
    }

    public function status()
    {
        $status = Cache::get('forge-deployment-status', [
            'status' => 'idle',
            'site_name' => null,
            'branch' => null,
            'commit' => null,
            'timestamp' => null,
        ]);

        return response()->json($status);
    }
}
