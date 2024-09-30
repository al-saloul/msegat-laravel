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
MSEGAT_BASEURL="https://www.msegat.com"
MSEGAT_USERNAME=your_msegat_username
MSEGAT_API_KEY=your_msegat_api_key
MSEGAT_USER_SENDER=your_msegat_sender_name
```
Alternatively, you can directly edit the `config/msegat.php` file to set the required configuration.

## Usage
To send an SMS message, use the `Msegat` facade:

```bash
use Alsaloul\Msegat\Msegat;

$response = Msegat::sendMessage('966123456789', 'Hello, this is a test message!');

if ($response['code'] === '1') {  
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
try {
    Msegat::sendMessage('966123456789', 'Testing error handling.');
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```
## Status Code and Messages
- `1` - Success

- `M0000` - Success

- `M0001` - Variables missing

- `M0002` - Invalid login info

- `M0022` - Exceed number of senders allowed

- `M0023` - Sender Name is active or under activation or refused

- `M0024` - Sender Name should be in English or number

- `M0025` - Invalid Sender Name Length

- `M0026` - Sender Name is already activated or not found

- `M0027` - Activation Code is not Correct

- `1010` - Variables missing

- `1020` - Invalid login info

- `1050` - MSG body is empty

- `1060` - Balance is not enough

- `1061` - MSG duplicated

- `1064` - Free OTP , Invalid MSG content you should use "Pin Code is: xxxx", "Verification Code: xxxx" or upgrade your account and activate your sender to send any content

- `1110` - Sender name is missing or incorrect

- `1120` - Mobile numbers is not correct

- `1140` - MSG length is too long

- `M0029` - Invalid Sender Name - Sender Name should contain only letters, numbers and the maximum length should be 11 characters

- `M0030` - Sender Name should ended with AD

- `M0031` - Maximum allowed size of uploaded file is 5 MB

- `M0032` - Only pdf,png,jpg and jpeg files are allowed!

- `M0033` - Sender Type should be normal or whitelist only

- `M0034` - Please Use POST Method

- `M0036` - There is no any sender
## Contributing
Contributions are welcome! Please follow these steps to contribute:

1. Fork the repository.
2. Create a new branch (`git checkout -b feature/new-feature`).
3. Commit your changes (`git commit -am 'Add new feature'`).
4. Push to the branch (`git push origin feature/new-feature`).
5. Create a new Pull Request.

## Support
If you have any questions or issues, feel free to open an issue on the GitHub repository or contact the author via email at `eng.alsaloul.mohammed@gmail.com`.

