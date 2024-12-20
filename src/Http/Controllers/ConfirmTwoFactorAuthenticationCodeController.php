<?php

namespace Wnx\TfaSudoMode\Http\Controllers;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\Events\ValidTwoFactorAuthenticationCodeProvided;

class ConfirmTwoFactorAuthenticationCodeController
{
    public function __construct(
        protected ResponseFactory $responseFactory,
    ) {}

    public function __invoke(Request $request, TwoFactorAuthenticationProvider $provider): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'numeric', 'min_digits:6', 'max_digits:6'],
        ]);

        /** @var User|Authenticatable $user */
        $user = $request->user();
        $code = $request->code;

        if (! $user->hasEnabledTwoFactorAuthentication()) {
            throw ValidationException::withMessages([
                'code' => [__('tfa-sudo-mode::translations.validation.two_factor_authentication_not_enabled')],
            ]);
        }

        $confirmed = $provider->verify(decrypt($user->two_factor_secret), $code);

        if (! $confirmed) {
            throw ValidationException::withMessages([
                'code' => [__('tfa-sudo-mode::translations.validation.invalid_two_factor_authentication_code')],
            ]);
        }

        event(new ValidTwoFactorAuthenticationCodeProvided($user));

        return $this->responseFactory->redirectToIntended('/');
    }
}
