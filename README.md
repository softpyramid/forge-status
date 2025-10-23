# Laravel Forge Webhook Status

Real-time deployment status indicator for Laravel Forge using webhooks.

## Installation

```bash
composer require softpyramid/forge-status
```

## Configuration

### 1. Publish config (optional):

```bash
php artisan vendor:publish --tag=forge-status-config
```

### 2. Set up Broadcasting

This package requires Laravel broadcasting to be configured. Use Pusher, Ably, or Laravel Reverb:

```env
BROADCAST_DRIVER=pusher
# or
BROADCAST_DRIVER=reverb
```

### 3. Add to .env (optional):

```env
FORGE_WEBHOOK_TOKEN=your-secret-token
FORGE_INDICATOR_POSITION=bottom-right
FORGE_AUTH_ONLY=true
```

### 4. Register Webhook in Laravel Forge

In your Forge site settings:

1. Go to "Webhooks"
2. Add new webhook URL: `https://yourdomain.com/forge-webhook`
3. Optionally add token parameter: `https://yourdomain.com/forge-webhook?token=your-secret-token`

## Usage

### Auto-inject into layouts

Add the component to your layout file (e.g., `resources/views/layouts/app.blade.php`):

```blade
<body>
    <!-- Your content -->
    
    <x-forge-deployment-indicator />
</body>
```

That's it! The indicator will automatically appear when deployments start and update in real-time.

## How It Works

1. Forge sends webhook POST request when deployment starts/finishes
2. Package receives webhook and broadcasts event via Laravel Echo
3. Frontend listens for broadcast and shows/updates indicator
4. No database storage - uses cache for current status only
5. Real-time updates via WebSockets

## Requirements

- PHP 8.1+
- Laravel 10.0 or 11.0+
- Laravel Broadcasting configured (Pusher/Reverb/Ably)
- Laravel Echo on frontend

## License

MIT License. See [LICENSE](LICENSE) file for details.

## Author

**Fakhar Zaman Khan** - [SoftPyramid](https://github.com/softpyramid)

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request
