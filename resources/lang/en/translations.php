<?php

return [
    // TODO
    'validation' => [
        'two_factor_authentication_not_enabled' => 'Two factor authentication is not enabled for this user.',
        'invalid_two_factor_authentication_code' => 'The provided two factor authentication code was invalid.',
    ],

    'responses' => [
        'json' => 'Two factor authentication required.',
    ],

    'challenge' => [
        'message' => 'Please confirm access to your account by entering the authentication code provided by your authenticator application.',
        'input_label' => 'Code',
        'button_label' => 'Verify Code',
    ],
];
