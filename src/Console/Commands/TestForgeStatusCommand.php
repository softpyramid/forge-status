<?php

namespace Softpyramid\ForgeStatus\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TestForgeStatusCommand extends Command
{
    protected $signature = 'forge-status:test {--status=success : Deployment status to test (deploying|success|failed)}';
    protected $description = 'Test the Forge Status package with sample data';

    public function handle()
    {
        $status = $this->option('status');
        
        if (!in_array($status, ['deploying', 'success', 'failed'])) {
            $this->error('Invalid status. Use: deploying, success, or failed');
            return 1;
        }

        $this->info('🧪 Testing Laravel Forge Status Package');
        $this->line('=====================================');
        
        // Test data based on user input
        $testData = [
            "status" => $status,
            "site_name" => "Test Site",
            "branch" => $status === 'deploying' ? 'main' : null,
            "commit" => "292ca985c2df2fb987983fdf755b5d4729434349",
            "timestamp" => now()->toIso8601String(),
        ];

        $this->line('');
        $this->info('📋 Test Data:');
        $this->line(json_encode($testData, JSON_PRETTY_PRINT));
        $this->line('');

        // Test 1: Store data in cache
        $this->info('🔧 Test 1: Storing deployment status in cache...');
        Cache::put('forge-deployment-status', $testData, config('forge-status.status_ttl'));
        
        if (Cache::has('forge-deployment-status')) {
            $this->info('✅ Cache storage successful!');
        } else {
            $this->error('❌ Cache storage failed!');
            return 1;
        }

        // Test 2: Retrieve data from cache
        $this->line('');
        $this->info('🔧 Test 2: Retrieving deployment status from cache...');
        $cachedData = Cache::get('forge-deployment-status');
        
        if ($cachedData && $cachedData['status'] === $status) {
            $this->info('✅ Cache retrieval successful!');
            $this->line('📊 Retrieved data: ' . json_encode($cachedData));
        } else {
            $this->error('❌ Cache retrieval failed!');
            return 1;
        }

        // Test 3: Test webhook endpoint
        $this->line('');
        $this->info('🔧 Test 3: Testing webhook endpoint...');
        
        try {
            $response = Http::timeout(10)->post(url('/forge-webhook'), $testData);
            
            if ($response->successful()) {
                $this->info('✅ Webhook endpoint test successful!');
                $this->line('📡 Response: ' . $response->body());
            } else {
                $this->error('❌ Webhook endpoint test failed!');
                $this->line('📡 Status: ' . $response->status());
                $this->line('📡 Response: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('❌ Webhook endpoint test failed: ' . $e->getMessage());
        }

        // Test 4: Test status endpoint
        $this->line('');
        $this->info('🔧 Test 4: Testing status endpoint...');
        
        try {
            $response = Http::timeout(10)->get(url('/forge-status'));
            
            if ($response->successful()) {
                $this->info('✅ Status endpoint test successful!');
                $this->line('📡 Response: ' . $response->body());
            } else {
                $this->error('❌ Status endpoint test failed!');
                $this->line('📡 Status: ' . $response->status());
                $this->line('📡 Response: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('❌ Status endpoint test failed: ' . $e->getMessage());
        }

        $this->line('');
        $this->info('🎉 Test completed!');
        
        if ($status === 'success') {
            $this->line('🖥️  Open your Laravel app in the browser to see the deployment success overlay!');
        } elseif ($status === 'failed') {
            $this->line('🖥️  Open your Laravel app in the browser to see the deployment failed overlay!');
        } else {
            $this->line('🖥️  Open your Laravel app in the browser to see the deployment in progress overlay!');
        }

        return 0;
    }
}
