# Msegat Laravel Package

This is a Laravel package that integrates with the Msegat SMS service. It simplifies the process of sending SMS messages through the Msegat API by providing an easy-to-use wrapper for Laravel applications.

## Features

- Send SMS messages easily using the Msegat API.
- Configurable credentials (username, API key, sender name) through the `.env` file.
- Designed specifically for Laravel applications.

## Requirements

- Laravel 8+ or 9+ or 10+ or 11
- PHP 7.4+ or PHP 8+

## Installation

To install the package, use Composer:

```bash
composer require al-saloul/msegat
```


## Publish Configuration
To publish the configuration file, use the following command:

```bash
php artisan vendor:publish --provider="Alsaloul\Msegat\MsegatServiceProvider"
```
This will create a configuration file named `msegat.php` in your `config` folder.

## Configuration
Add the following environment variables to your `.env` file:


```bash
MSEGAT_USERNAME=your_msegat_username
MSEGAT_API_KEY=your_msegat_api_key
MSEGAT_USER_SENDER=your_msegat_sender_name
```
Alternatively, you can directly edit the `config/msegat.php` file to set the required configuration.

## Usage
To send an SMS message, use the `Msegat` facade:

```bash
use Alsaloul\Msegat\Facades\Msegat;

$response = Msegat::sendMessage('966123456789', 'Hello, this is a test message!');

if ($response->status === 'success') {
    echo "Message sent successfully!";
} else {
    echo "Failed to send message.";
}
```
## Methods
`Msegat::sendMessage($numbers, $message)`
- $numbers: The phone numbers to send the message to, separated by commas. (e.g., `96651xxxxxxx`,`96652xxxxxxx`)
- $message: The content of the message.

This method will send the message to the given numbers using the Msegat API.

`Msegat::data($numbers, $message)`

This method generates the JSON payload required for sending SMS messages. It's used internally by the `sendMessage()` method.

## Examples
### Sending a Single Message

```bash
Msegat::sendMessage('9665xxxxxxxx', 'Hello from Msegat!');
```

### Sending Multiple Messages
```bash
$numbers = '96651xxxxxxx,96652xxxxxxx';
$message = 'This is a broadcast message.';

$response = Msegat::sendMessage($numbers, $message);
```

### Handling Errors
If credentials or required parameters are not set, exceptions will be thrown:

```bash
$numbers = '96651xxxxxxx,96652xxxxxxx';
$message = 'This is a broadcast message.';

$response = Msegat::sendMessage($numbers, $message);
```

## Contributing
Contributions are welcome! Please follow these steps to contribute:

1. Fork the repository.
2. Create a new branch (`git checkout -b feature/new-feature`).
3. Commit your changes (`git commit -am 'Add new feature'`).
4. Push to the branch (`git push origin feature/new-feature`).
5. Create a new Pull Request.

## Support
If you have any questions or issues, feel free to open an issue on the GitHub repository or contact the author via email at `eng.alsaloul.mohammed@gmail.com`.

