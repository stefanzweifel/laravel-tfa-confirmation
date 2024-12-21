<?php

use Wnx\TfaConfirmation\Http\Controllers\ConfirmTwoFactorAuthenticationCodeController;
use Wnx\TfaConfirmation\Http\Controllers\TwoFactorAuthenticationChallengeController;
use Wnx\TfaConfirmation\Http\Responses\DefaultJsonResponse;

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
