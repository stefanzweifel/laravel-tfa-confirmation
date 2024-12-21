<?php

namespace Wnx\TfaConfirmation\Contracts;

use Illuminate\Http\Request;

interface ConfirmsTwoFactor
{
    public function shouldConfirmTwoFactor(Request $request): bool;

    public function twoFactorConfirmationTimeout(): int;
}
