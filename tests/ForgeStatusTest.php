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

    public function test_routes_are_registered()
    {
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('forge-status.webhook'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('forge-status.check'));
    }

    public function test_webhook_route_accepts_post_requests()
    {
        $response = $this->post('/forge-webhook', [
            'status' => 'deploying',
            'site_name' => 'Test Site',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Webhook received']);
    }

    public function test_status_route_accepts_get_requests()
    {
        $response = $this->get('/forge-status');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'site_name',
            'branch',
            'commit',
            'timestamp',
        ]);
    }
}
