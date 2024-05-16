# Fonial API Client

This PHP library provides an interface for interacting with the Fonial API.
It follows PSR-18 (HTTP Client) and PSR-3 (Logger) standards for HTTP requests and logging.

Fonial is not related to this library, it's created solely by the fucodo GmbH.

## Features

- Authenticate with the Fonial API
- Retrieve available IP devices
- Retrieve available numbers
- Retrieve call records within a specified time frame
- Retrieve incoming connections within a specified time frame

## Requirements

- PHP 8.0 or higher
- Composer
- PSR-18 HTTP Client implementation (e.g., Guzzle)
- PSR-17 HTTP Factory implementation (e.g., Nyholm PSR-7)
- PSR-3 Logger implementation (e.g., Monolog)
- fonial account
- fonial api access 
  - https://www.fonial.de/hilfe/api
  - https://www.fonial.de/telefonanlage/funktionen/api/

## Installation

Install the library via Composer:

```sh
composer require fucodo/api-fonial
```

## Usage

### Initialization

Before using the API client, you need to initialize it with a PSR-18 HTTP Client, PSR-17 HTTP Factory, and a PSR-3 Logger.

```php
require 'vendor/autoload.php';

use GuzzleHttp\Client as GuzzleClient;
use Nyholm\Psr7\Factory\Psr17Factory;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Fucodo\ApiFonial\FonialApi;

// Initialize the HTTP client and request factory
$client = new GuzzleClient();
$psr17Factory = new Psr17Factory();

// Initialize the logger
$logger = new Logger('fonial');
$logger->pushHandler(new StreamHandler('php://stdout', Logger::INFO));

// Create the Fonial API client
$fonial = new FonialApi($client, $psr17Factory, $logger);
```

### Authentication

To authenticate with the Fonial API, use the `auth` method with your username and password.

```php
try {
    $fonial->auth('your_username', 'your_password');
    echo "Authentication successful.\n";
} catch (AuthenticationException $e) {
    echo "Authentication failed: " . $e->getMessage() . "\n";
} catch (SessionException $e) {
    echo "Session error: " . $e->getMessage() . "\n";
}
```

### Retrieve IP Devices

To retrieve the available IP devices, use the `deviceGet` method.

```php
$devices = $fonial->deviceGet();
print_r($devices);
```

### Retrieve Numbers

To retrieve the available numbers, use the `numbersGet` method.

```php
$numbers = $fonial->numbersGet();
print_r($numbers);
```

### Retrieve Call Records

To retrieve call records within a specified time frame, use the `evnGet` method.

```php
$callRecords = $fonial->evnGet('2023-01-01 00:00:00', '2023-01-31 23:59:59');
print_r($callRecords);
```

### Retrieve Incoming Connections

To retrieve incoming connections within a specified time frame, use the `journalGet` method.

```php
$incomingConnections = $fonial->journalGet('2023-01-01 00:00:00', '2023-01-31 23:59:59');
print_r($incomingConnections);
```

## Exception Handling

The library throws specific exceptions for authentication and session errors.

- `Fucodo\ApiFonial\Exception\AuthenticationException`: Thrown when authentication fails.
- `Fucodo\ApiFonial\Exception\SessionException`: Thrown when there is a session error.

## License

This library is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.

## Contributing

Contributions are welcome! Please open an issue or submit a pull request on GitHub.

## Contact

For any questions or support, please open an issue on GitHub.

---

This README file provides a comprehensive overview of the Fonial API Client, including installation instructions, usage examples, exception handling, and contact information. Follow the best practices for a clear and informative README to help users understand and utilize the library effectively.