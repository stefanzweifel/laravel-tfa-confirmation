<?php

namespace Wnx\TfaConfirmation\Listeners;

use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Date;
use Laravel\Fortify\Events\ValidTwoFactorAuthenticationCodeProvided;

readonly class StoreTwoFactorConfirmedAtInSessionListener
{
    public function __construct(protected Session $session) {}

    public function handle(ValidTwoFactorAuthenticationCodeProvided $event): void
    {
        // Store the two factor confirmed at timestamp in the session,
        // so that users are not immediately asked for a code again.
        $this->session->put(
            config('tfa-sudo-mode.session_key'),
            Date::now()->unix()
        );
    }
}
