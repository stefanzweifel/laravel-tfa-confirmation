# Ask for the two-factor authentication code of a user before accessing sensitive routes or actions.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/stefanzweifel/laravel-tfa-sudo-mode.svg?style=flat-square)](https://packagist.org/packages/wnx/laravel-tfa-sudo-mode)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/stefanzweifel/laravel-tfa-sudo-mode/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/stefanzweifel/laravel-tfa-sudo-mode/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/stefanzweifel/laravel-tfa-sudo-mode/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/stefanzweifel/laravel-tfa-sudo-mode/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/stefanzweifel/laravel-tfa-sudo-mode.svg?style=flat-square)](https://packagist.org/packages/wnx/laravel-tfa-sudo-mode)

Protect sensitive routes or actions with a confirmation-screen and ask for the two-factor authentication code of a user. Users are not asked for a confirmation again for a given time period. (Similar to the [Password Confirmation](https://laravel.com/docs/master/authentication#password-confirmation) feature of Laravel.)

The package uses [Laravel Fortify](https://laravel.com/docs/master/fortify) under the hood to confirm the two-factor authentication code.

## Installation

You can install the package via composer:

```bash
composer require wnx/laravel-tfa-sudo-mode
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="tfa-sudo-mode-config"
```

This is the contents of the published config file:

```php
<?php

use Wnx\TfaSudoMode\Http\Controllers\ConfirmTwoFactorAuthenticationCodeController;
use Wnx\TfaSudoMode\Http\Controllers\TwoFactorAuthenticationChallengeController;
use Wnx\TfaSudoMode\Http\Responses\DefaultJsonResponse;

return [
    /**
     * Enable or disable two-factor authentication sudo mode
     */
    'enabled' => env('TFA_SUDO_MODE_ENABLED', true),

    /**
     * The session key that is used to store the timestamp of the last time
     * the user confirmed their two-factor authentication code.
     */
    'session_key' => 'auth.two_factor_confirmed_at',

    /**
     * The amount of time in seconds the sudo mode is active.
     * Users will not be asked to enter their two-factor authentication code again for this amount of time.
     */
    'timeout' => env('TFA_SUDO_MODE_TIMEOUT', 60 * 60 * 24), // 24 hours

    /**
     * The view that should be returned when the user needs to confirm their two-factor authentication code.
     * You should publish the views to your application to customize the challenge view.
     */
    'challenge_view' => 'tfa-sudo-mode::challenge',

    /**
     * Controller used to show the two-factor authentication challenge view.
     */
    'challenge_controller' => TwoFactorAuthenticationChallengeController::class,

    /**
     * Controller used to confirm the two-factor authentication code entered by the user on the challenge view.
     * If you customize this controller, make sure to dispatch the `\Laravel\Fortify\Events\ValidTwoFactorAuthenticationCodeProvided` event.
     */
    'confirmation_controller' => ConfirmTwoFactorAuthenticationCodeController::class,

    /**
     * The response that should be returned when the user needs to confirm their
     * two-factor authentication code, but the request expects a JSON response.
     */
    'json_response' => DefaultJsonResponse::class,
];
```

The defaul *challenge*-view is not styled. We highly recommend you publish the views and customize them to your design.

```bash
php artisan vendor:publish --tag="tfa-sudo-mode-views"
```

## Usage

To protect routes with a two-factor confirmation challenge add the `\Wnx\TfaSudoMode\Http\Middleware\RequireTwoFactorAuthenticationConfirmation`-middleware to your routes.

```php
// routes/web.php

// Protect a single route
Route::get('/super-important-route', SuperImportantController::class)
    ->middleware([
        // Use Middleware directly
        \Wnx\TfaSudoMode\Http\Middleware\RequireTwoFactorAuthenticationConfirmation::class,

        // Use Middleware alias
        'require_twofactor_confirmation',
    ]);

// Protect a group of routes
Route::middleware([
    'auth:sanctum',
    'verified',
    \Wnx\TfaSudoMode\Http\Middleware\RequireTwoFactorAuthenticationConfirmation::class,
])->group(function () {
    // Routes that need to be protected by two factor authentication
});  
```

> [!NOTE]
> If a given user does not have two-factor authentication enabled, the middleware is bypassed and the user will not be asked to confirm their two-factor authentication code.
> If you want certain routes only to be available to users with two-factor authentication enabled, you have to write a custom middleware that checks this condition.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Stefan Zweifel](https://github.com/stefanzweifel)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
