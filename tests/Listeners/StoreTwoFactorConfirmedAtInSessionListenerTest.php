<?php

use PragmaRX\Google2FA\Google2FA;
use Workbench\App\Models\User;

use function Pest\Laravel\freezeTime;

it('listens to the ValidTwoFactorAuthenticationCodeProvided and stores a timestamp in the session when a valid two factor authentication code was verified', function (): void {
    freezeTime();

    $user = User::factory()->hasTwoFactorAuthenticationEnabled()->create();
    $currentOtp = app(Google2FA::class)->getCurrentOtp(decrypt($user->two_factor_secret));

    $response = $this->actingAs($user)
        ->from(route('auth.two-factor-authentication.challenge'))
        ->post(route('auth.two-factor-authentication.confirm'), [
            'code' => $currentOtp,
        ]);

    $response->assertRedirect('/');
    $response->assertSessionHas(config('tfa-confirmation.session_key'));
});
