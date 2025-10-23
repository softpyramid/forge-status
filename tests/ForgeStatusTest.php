<?php

namespace Softpyramid\ForgeStatus\Tests;

use Illuminate\Support\Facades\Cache;

class ForgeStatusTest extends TestCase
{

    public function test_webhook_controller_stores_status_in_cache()
    {
        $response = $this->postJson('/forge-webhook', [
            'status' => 'deploying',
            'site_name' => 'Test Site',
            'branch' => 'main',
            'commit_hash' => 'abc123',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Webhook received']);

        $cachedStatus = Cache::get('forge-deployment-status');
        $this->assertEquals('deploying', $cachedStatus['status']);
        $this->assertEquals('Test Site', $cachedStatus['site_name']);
        $this->assertEquals('main', $cachedStatus['branch']);
        $this->assertEquals('abc123', $cachedStatus['commit']);
    }

    public function test_status_controller_returns_cached_status()
    {
        Cache::put('forge-deployment-status', [
            'status' => 'deploying',
            'site_name' => 'Test Site',
            'branch' => 'main',
            'commit' => 'abc123',
            'timestamp' => now()->toIso8601String(),
        ], 300);

        $response = $this->getJson('/forge-status');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'deploying',
            'site_name' => 'Test Site',
            'branch' => 'main',
            'commit' => 'abc123',
        ]);
    }

    public function test_status_controller_returns_default_when_no_cache()
    {
        Cache::forget('forge-deployment-status');

        $response = $this->getJson('/forge-status');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'idle',
            'site_name' => null,
            'branch' => null,
            'commit' => null,
            'timestamp' => null,
        ]);
    }
}
