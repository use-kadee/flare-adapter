# Kadee Flare Adapter

Send Flare error reports to [Kadee](https://usekadee.com) for AI-powered fixing.

## Installation

```bash
composer require spatie/laravel-flare use-kadee/flare-adapter
```

## Laravel Usage

1. Register Flare in your `bootstrap/app.php`:

```php
->withExceptions(function (Exceptions $exceptions) {
    \Spatie\LaravelFlare\Facades\Flare::handles($exceptions);
})->create();
```

2. Add your Kadee credentials to `.env`:

```env
KADEE_PROJECT=your-project-uuid
KADEE_KEY=your-project-secret
```

3. That's it! The package auto-configures via Laravel's service provider.

## Configuration

Publish the config file (optional):

```bash
php artisan vendor:publish --tag=kadee-config
```

Available options:

```php
return [
    'project' => env('KADEE_PROJECT'),
    'key' => env('KADEE_KEY'),
    'endpoint' => env('KADEE_ENDPOINT', 'https://usekadee.com/api/ingest'),
    'timeout' => env('KADEE_TIMEOUT', 5),
];
```

## Requirements

- PHP 8.2+
- Laravel 11+
- `spatie/laravel-flare` ^2.2

## License

MIT
