<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])
    ->group(function () {

        // Route to show the two-factor authentication challenge view
        Route::get('/auth/two-factor-authentication/confirm', config('tfa-sudo-mode.challenge_controller'))
            ->name('auth.two-factor-authentication.challenge');

        // Route to confirm the two-factor authentication code by emitting the ValidTwoFactorAuthenticationCodeProvided event.
        Route::post('/auth/two-factor-authentication/confirm', config('tfa-sudo-mode.confirmation_controller'))
            ->name('auth.two-factor-authentication.confirm');
    });
