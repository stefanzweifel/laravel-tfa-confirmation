<?php

namespace Wnx\TfaConfirmation\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class RequireTwoFactorAuthenticationConfirmation
{
    protected int $timeout;

    public function __construct(
        protected ResponseFactory $responseFactory,
        protected UrlGenerator $urlGenerator,
        $timeout = null,
    ) {
        $this->timeout = $timeout ?: config('tfa-sudo-mode.timeout');
    }

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldConfirmTwoFactor($request)) {
            if ($request->expectsJson()) {
                return app(config('tfa-sudo-mode.json_response'))();
            }

            return $this->responseFactory->redirectGuest(
                $this->urlGenerator->route('auth.two-factor-authentication.challenge')
            );
        }

        return $next($request);
    }

    protected function shouldConfirmTwoFactor(Request $request): bool
    {
        // Do not ask for two-factor authentication if sudo mode has been disabled.
        if (config('tfa-sudo-mode.enabled') === false) {
            return false;
        }

        // Do not ask for two-factor authentication if the user does not have two-factor authentication enabled.
        if ($request->user()->hasEnabledTwoFactorAuthentication() === false) {
            return false;
        }

        // Calculate the time since the user last confirmed their two-factor authentication code.
        $sessionValue = $request->session()->get(config('tfa-sudo-mode.session_key'));
        $confirmedAtTimestamp = time() - $sessionValue;

        // Return true if the user has not confirmed their two-factor authentication code within the timeout.
        return $confirmedAtTimestamp > $this->timeout;
    }
}
