# TrackerGG Apex PHP Wrapper

A PHP wrapper for the TrackerGG Apex Legends API.

## Installation

Install via Composer:

```bash
composer require cocoiti/tracker-gg-apex-php
```

## Requirements

- PHP 8.0 or higher
- Guzzle HTTP client

## Usage

### Basic Usage

```php
<?php

use Cocoiti\TrackerGG\Apex\Client;

// Initialize the client with your API key
$client = new Client('your-api-key');

// Get a player profile
try {
    $profile = $client->getProfile('username', 'origin');
    
    echo "Player: " . $profile->getPlayerName() . "\n";
    echo "Level: " . $profile->getLevel() . "\n";
    echo "Rank RP: " . $profile->getRankRP() . "\n";
    echo "Arena Rank RP: " . $profile->getArenaRankRP() . "\n";
    echo "Total Kills: " . $profile->getKills() . "\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
```


## Supported Platforms

Currently supported platforms:
- `origin` (default)

## Available Methods

### Client Methods

- `getProfile(string $username, string $platform = 'origin')`: Get player profile

### Profile Methods

- `getPlayerName()`: Get player username
- `getLevel()`: Get player level
- `getRankRP()`: Get ranked RP points
- `getArenaRankRP()`: Get arena ranked RP points
- `getKills()`: Get total kills
- `getResponse()`: Get raw API response

## Exception Handling

The library uses custom exceptions:

- `TrackerGGException`: Base exception class
- `ApiException`: API-related errors (network, HTTP status)
- `InvalidDataException`: Invalid input data or response structure

```php
use Cocoiti\TrackerGG\Apex\Exception\ApiException;
use Cocoiti\TrackerGG\Apex\Exception\InvalidDataException;

try {
    $profile = $client->getProfile('username');
} catch (InvalidDataException $e) {
    // Handle invalid data (e.g., invalid username format)
    echo "Invalid data: " . $e->getMessage();
} catch (ApiException $e) {
    // Handle API errors (e.g., network issues, API limits)
    echo "API error: " . $e->getMessage();
}
```

## Development

### Running Tests

```bash
composer install
vendor/bin/phpunit
```

### Testing Requirements

- PHPUnit 9.0+

## API Key

You need to obtain an API key from [TrackerGG](https://tracker.gg/developers) to use this library.

## License

This project is open source. Please check the license file for more information.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.