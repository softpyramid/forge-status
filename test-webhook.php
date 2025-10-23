<?php

/**
 * Test script for Laravel Forge Status Package
 * 
 * This script simulates a webhook request from Laravel Forge
 * to test the deployment status overlay functionality.
 */

// Test data from user
$testData = [
    "status" => "success",
    "site_name" => "Site",
    "branch" => null,
    "commit" => "292ca985c2df2fb987983fdf755b5d4729434349",
    "timestamp" => "2025-10-23T22:47:32+00:00"
];

echo "🚀 Testing Laravel Forge Status Package\n";
echo "=====================================\n\n";

echo "📋 Test Data:\n";
echo json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

echo "🔧 Testing webhook endpoint...\n";

// Simulate webhook request
$webhookUrl = 'http://localhost:8000/forge-webhook';
$postData = json_encode($testData);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $webhookUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($postData)
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ cURL Error: $error\n";
    exit(1);
}

echo "📡 HTTP Status: $httpCode\n";
echo "📨 Response: $response\n\n";

if ($httpCode === 200) {
    echo "✅ Webhook test successful!\n";
    echo "🎯 The overlay should now show 'Deployment Complete!' with a green checkmark.\n";
    echo "⏰ It will auto-hide after 5 seconds.\n\n";
    
    echo "🔍 Testing status endpoint...\n";
    
    // Test status endpoint
    $statusUrl = 'http://localhost:8000/forge-status';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $statusUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $statusResponse = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "📡 Status HTTP Code: $statusCode\n";
    echo "📊 Status Response: $statusResponse\n\n";
    
    if ($statusCode === 200) {
        echo "✅ Status endpoint test successful!\n";
        echo "🎉 All tests passed! The package is working correctly.\n";
    } else {
        echo "❌ Status endpoint test failed!\n";
    }
} else {
    echo "❌ Webhook test failed!\n";
    echo "🔧 Make sure your Laravel application is running on localhost:8000\n";
    echo "🔧 Ensure the package is properly installed and routes are registered\n";
}

echo "\n📝 Instructions Test:\n";
echo "1. Make sure your Laravel app is running: php artisan serve\n";
echo "2. Visit your app in the browser\n";
echo "3. Run this test: php test-webhook.php\n";
echo "4. Check the browser - you should see the deployment success overlay!\n";
