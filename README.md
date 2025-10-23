# Laravel Forge Webhook Status

[![Latest Version on Packagist](https://img.shields.io/packagist/v/softpyramid/forge-status.svg?style=flat-square)](https://packagist.org/packages/softpyramid/forge-status)
[![Total Downloads](https://img.shields.io/packagist/dt/softpyramid/forge-status.svg?style=flat-square)](https://packagist.org/packages/softpyramid/forge-status)
[![Laravel Version](https://img.shields.io/badge/Laravel-10%20%7C%2011%20%7C%2012-red.svg?style=flat-square)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg?style=flat-square)](https://php.net)

Real-time deployment status indicator for Laravel Forge using webhooks. Get instant visual feedback when your deployments start, succeed, or fail.

## Features

- 🚀 **Real-time Updates** - WebSocket-powered live deployment status
- 🎯 **No Database Required** - Uses cache only for current status
- 🔗 **Webhook-driven** - Receives deployment events from Laravel Forge
- 🎨 **Auto-injection** - Automatically appears in your Laravel layouts
- ⚙️ **Configurable** - Customizable position and authentication
- 🎭 **Modern UI** - Beautiful Tailwind CSS styling
- 📱 **Responsive** - Works on all device sizes

## Requirements

- PHP 8.2 or higher
- Laravel 10, 11, or 12
- Laravel Broadcasting configured (Pusher/Reverb/Ably)
- Laravel Echo on frontend

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

## Laravel Version Compatibility

| Laravel Version | Package Version | PHP Version |
|----------------|-----------------|-------------|
| Laravel 12.x   | ^1.0            | ^8.2        |
| Laravel 11.x   | ^1.0            | ^8.2        |
| Laravel 10.x   | ^1.0            | ^8.1        |

## Testing

```bash
# Run tests
composer test

# Run tests with coverage
composer test-coverage
```

## Troubleshooting

### Laravel 12 Compatibility Issues

If you encounter issues with Laravel 12, ensure you have:

1. **PHP 8.2+** installed
2. **Updated dependencies** - run `composer update`
3. **Cleared caches** - run `php artisan config:clear && php artisan cache:clear`

### Broadcasting Not Working

Make sure you have:

1. **Broadcasting configured** in your `.env`:
   ```env
   BROADCAST_DRIVER=pusher
   # or
   BROADCAST_DRIVER=reverb
   ```

2. **Laravel Echo** installed and configured on your frontend

3. **WebSocket server** running (if using Reverb)

### Webhook Not Receiving Data

Verify:

1. **Webhook URL** is correctly registered in Laravel Forge
2. **Token verification** is properly configured (if using)
3. **Route is accessible** - check `/forge-webhook` endpoint

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
