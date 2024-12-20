<?php

use Laravel\Fortify\Events\ValidTwoFactorAuthenticationCodeProvided;
use PragmaRX\Google2FA\Google2FA;
use Wnx\TfaSudoMode\Listeners\StoreTwoFactorConfirmedAtInSessionListener;
use Workbench\App\Models\User;

use function Pest\Laravel\freezeTime;

it('listens to the ValidTwoFactorAuthenticationCodeProvided and stores a timestamp in the session when a valid two factor authentication code was verified', function (): void {
    Event::listen(ValidTwoFactorAuthenticationCodeProvided::class, [StoreTwoFactorConfirmedAtInSessionListener::class, 'handle']);

    freezeTime();

    $user = User::factory()->hasTwoFactorAuthenticationEnabled()->create();
    $currentOtp = app(Google2FA::class)->getCurrentOtp(decrypt($user->two_factor_secret));

    $response = $this->actingAs($user)
        ->from(route('auth.two-factor-authentication.challenge'))
        ->post(route('auth.two-factor-authentication.confirm'), [
            'code' => $currentOtp,
        ]);

    $response->assertRedirect('/');
    $response->assertSessionHas(config('tfa-sudo-mode.session_key'));
});
