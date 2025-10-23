<?php

return [
    // Webhook verification token (optional security)
    'webhook_token' => env('FORGE_WEBHOOK_TOKEN'),
    
    // Position: 'top-right', 'top-left', 'bottom-right', 'bottom-left'
    'position' => env('FORGE_INDICATOR_POSITION', 'bottom-right'),
    
    // Show only to authenticated users
    'auth_only' => env('FORGE_AUTH_ONLY', true),
    
    // Cache TTL for deployment status (in seconds)
    'status_ttl' => 300, // 5 minutes
];
