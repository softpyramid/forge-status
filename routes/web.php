<?php

use Illuminate\Support\Facades\Route;
use Softpyramid\ForgeStatus\Http\Controllers\ForgeWebhookController;
use Softpyramid\ForgeStatus\Http\Controllers\ForgeStatusController;

// Public webhook endpoint for Forge
Route::post('/forge-webhook', ForgeWebhookController::class)
    ->name('forge-status.webhook');

// Status endpoint for frontend
Route::get('/forge-status', [ForgeStatusController::class, 'status'])
    ->name('forge-status.check');
