# Msegat Laravel Package

This is a Laravel package that integrates with the Msegat SMS service. It simplifies the process of sending SMS messages through the Msegat API by providing an easy-to-use wrapper for Laravel applications.

## Features

- Send SMS messages easily using the Msegat API.
- Configurable credentials (username, API key, sender name) through the `.env` file.
- Accepts recipients as a string, a comma separated string, or an (even nested) array.
- Fails fast with a clear exception when credentials, recipients, or the message body are missing.
- Configurable timeout and retry policy.
- Fully testable: use Laravel's `Http::fake()` or inject `MsegatClient` directly.

## Requirements

| Package | PHP             | Laravel             |
|---------|-----------------|---------------------|
| 2.x     | 8.0 &ndash; 8.5 | 9, 10, 11, 12, 13   |

The support matrix is verified in CI for every combination:

| Laravel | PHP 8.0 | PHP 8.1 | PHP 8.2 | PHP 8.3 | PHP 8.4 | PHP 8.5 |
|---------|:-------:|:-------:|:-------:|:-------:|:-------:|:-------:|
| 9.x     | ✅      | ✅      | ✅      | —       | —       | —       |
| 10.x    | —       | ✅      | ✅      | ✅      | ✅      | —       |
| 11.x    | —       | —       | ✅      | ✅      | ✅      | —       |
| 12.x    | —       | —       | ✅      | ✅      | ✅      | ✅      |
| 13.x    | —       | —       | —       | ✅      | ✅      | ✅      |

Composer resolves the right combination automatically, so you never need to pin a version yourself.

## Installation

To install the package, use Composer:

```bash
composer require al-saloul/msegat
```

The service provider and the `Msegat` alias are registered automatically through Laravel's package discovery.

## Publish Configuration

To publish the configuration file, use the following command:

```bash
php artisan vendor:publish --tag=msegat-config
```

This will create a configuration file named `msegat.php` in your `config` folder. The original provider based form keeps working too:

```bash
php artisan vendor:publish --provider="Alsaloul\Msegat\MsegatServiceProvider"
```

## Configuration

Add the following environment variables to your `.env` file:

```bash
MSEGAT_BASEURL="https://www.msegat.com"
MSEGAT_USERNAME=your_msegat_username
MSEGAT_API_KEY=your_msegat_api_key
MSEGAT_USER_SENDER=your_msegat_sender_name
```

Optionally, tune the HTTP behaviour:

```bash
MSEGAT_TIMEOUT=30       # per request timeout, in seconds
MSEGAT_RETRIES=0        # extra attempts when the gateway is unreachable
MSEGAT_RETRY_DELAY=250  # pause between attempts, in milliseconds
```

Alternatively, you can directly edit the `config/msegat.php` file to set the required configuration.

## Usage

To send an SMS message, use the `Msegat` facade:

```php
use Alsaloul\Msegat\Facades\Msegat;

$response = Msegat::sendMessage('966123456789', 'Hello, this is a test message!');

if ($response['code'] === '1') {
    echo "Message sent successfully!";
} else {
    echo "Failed to send message.";
}
```

### Dependency injection

The client is bound as a singleton, so you can inject it anywhere instead of reaching for the facade:

```php
use Alsaloul\Msegat\MsegatClient;

class OrderController
{
    public function __construct(private MsegatClient $msegat)
    {
    }

    public function store()
    {
        $this->msegat->sendMessage('9665xxxxxxxx', 'Your order has been received.');
    }
}
```

## Methods

`Msegat::sendMessage($numbers, $message)`

- `$numbers`: the recipients. Accepts a single number (`'9665xxxxxxxx'`), a comma separated string (`'96651xxxxxxx,96652xxxxxxx'`), or an array (`['96651xxxxxxx', '96652xxxxxxx']`). Values are trimmed, de-duplicated, and blanks are dropped.
- `$message`: the content of the message.

Returns the decoded gateway response as an array. If the gateway answers in plain text rather than JSON, the response is normalised to `['code' => ..., 'message' => ...]` so `$response['code']` is always safe to read.

`Msegat::payload($numbers, $message)` &mdash; builds and validates the payload sent to the gateway. `Msegat::data($numbers, $message)` is a backwards compatible alias of the same method.

`Msegat::normalizeNumbers($numbers)` &mdash; returns the comma separated recipient list the gateway expects.

## Examples

### Sending a Single Message

```php
Msegat::sendMessage('9665xxxxxxxx', 'Hello from Msegat!');
```

### Sending to Multiple Recipients

```php
$numbers = ['96651xxxxxxx', '96652xxxxxxx'];
$message = 'This is a broadcast message.';

$response = Msegat::sendMessage($numbers, $message);
```

A comma separated string works just as well:

```php
Msegat::sendMessage('96651xxxxxxx,96652xxxxxxx', 'This is a broadcast message.');
```

### Handling Errors

If credentials or required parameters are not set, a `MsegatException` is thrown:

```php
use Alsaloul\Msegat\Exceptions\MsegatException;

try {
    Msegat::sendMessage('966123456789', 'Testing error handling.');
} catch (MsegatException $e) {
    echo "Error: " . $e->getMessage();
}
```

`MsegatException` extends `RuntimeException`, so existing `catch (Exception $e)` blocks keep working. It is thrown when:

- a required credential (`username`, `user_sender`, `api_key`) is blank;
- no usable recipient was supplied;
- the message body is empty;
- the gateway could not be reached, or returned an empty response.

### Testing

Because the package uses Laravel's HTTP client, `Http::fake()` intercepts every request:

```php
use Illuminate\Support\Facades\Http;

Http::fake([
    '*' => Http::response(['code' => '1', 'message' => 'Success']),
]);

Msegat::sendMessage('9665xxxxxxxx', 'Hello!');

Http::assertSent(fn ($request) => $request->data()['msg'] === 'Hello!');
```

## Upgrading from 1.x

The public API is unchanged, and `use Alsaloul\Msegat\Msegat;` with static calls keeps working.

- Laravel 8 and PHP 7.4 are no longer supported. Laravel 9 on PHP 8.0 remains supported, so most 1.x installs upgrade without touching their runtime.
- The `Msegat` facade is now functional. Previously its accessor pointed at an unbound `msegat` key, so calling it threw a binding resolution error; only the concrete class worked.
- `sendMessage()` used to require an array and raised a PHP error when handed the string the documentation showed. Both are now accepted.
- Prefer `Alsaloul\Msegat\Facades\Msegat` (or inject `Alsaloul\Msegat\MsegatClient`) in new code. `Alsaloul\Msegat\Msegat` remains as a backwards compatible alias.

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

## Testing the Package

```bash
composer install
composer test
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
