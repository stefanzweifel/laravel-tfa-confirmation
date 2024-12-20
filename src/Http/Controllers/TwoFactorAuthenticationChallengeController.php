<?php

namespace Wnx\TfaSudoMode\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TwoFactorAuthenticationChallengeController
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        if ($request->user()->hasEnabledTwoFactorAuthentication() === false) {
            return redirect('/');
        }

        return view(config('tfa-sudo-mode.challenge_view'));
    }
}
