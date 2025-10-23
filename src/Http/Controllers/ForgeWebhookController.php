<?php

namespace Softpyramid\ForgeStatus\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class ForgeWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        // Optional: Verify webhook token
        if ($token = config('forge-status.webhook_token')) {
            if ($request->input('token') !== $token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }

        $status = $request->input('status'); // 'deploying', 'success', 'failed'
        $siteName = $request->input('site_name', 'Site');
        $branch = $request->input('branch');
        $commit = $request->input('commit_hash');

        // Store current status in cache (no database)
        $deploymentData = [
            'status' => $status,
            'site_name' => $siteName,
            'branch' => $branch,
            'commit' => $commit,
            'timestamp' => now()->toIso8601String(),
        ];

        Cache::put('forge-deployment-status', $deploymentData, config('forge-status.status_ttl'));

        return response()->json(['message' => 'Webhook received']);
    }
}
