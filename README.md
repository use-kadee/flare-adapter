# Kadee Flare Adapter

Send Flare error reports to [Kadee](https://kadee.io) for AI-powered fixing.

## Installation

```bash
composer require use-kadee/flare-adapter
```

## Laravel Usage

1. Add your Kadee project key to `.env`:

```env
KADEE_KEY=your-project-uuid
```

2. That's it! The package auto-configures via Laravel's service provider.

## Base PHP Usage

```php
<?php

require 'vendor/autoload.php';

use Kadee\FlareAdapter\Kadee;

Kadee::make('your-project-uuid')
    ->registerFlareHandlers();

// Your app code...
```

## Configuration

Publish the config file (optional):

```bash
php artisan vendor:publish --tag=kadee-config
```

Available options:

```php
return [
    'key' => env('KADEE_KEY'),
    'endpoint' => env('KADEE_ENDPOINT', 'https://kadee.io/api/ingest'),
    'timeout' => env('KADEE_TIMEOUT', 5),
];
```

## Requirements

- PHP 8.2+
- `spatie/flare-client-php` ^2.0

## License

MIT
