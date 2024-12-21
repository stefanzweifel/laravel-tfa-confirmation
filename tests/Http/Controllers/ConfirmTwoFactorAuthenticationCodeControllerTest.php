<?php

use Illuminate\Support\Facades\Event;
use Laravel\Fortify\Events\ValidTwoFactorAuthenticationCodeProvided;
use PragmaRX\Google2FA\Google2FA;
use Workbench\App\Models\User;

it('confirms valid two factor authentication code stores timestamp in session and redirects user to intended url', function (): void {
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

it('dispatches event that valid two factor code was provided', function () {
    Event::fake([ValidTwoFactorAuthenticationCodeProvided::class]);

    $user = User::factory()->hasTwoFactorAuthenticationEnabled()->create();

    $currentOtp = app(Google2FA::class)->getCurrentOtp(decrypt($user->two_factor_secret));

    $response = $this->actingAs($user)
        ->from(route('auth.two-factor-authentication.challenge'))
        ->post(route('auth.two-factor-authentication.confirm'), [
            'code' => $currentOtp,
        ]);

    $response->assertRedirect('/');

    Event::assertDispatched(ValidTwoFactorAuthenticationCodeProvided::class);
});

it('throws validation error if two factor authentication code is invalid', function (): void {
    $user = User::factory()->hasTwoFactorAuthenticationEnabled()->create();

    $response = $this->actingAs($user)
        ->from(route('auth.two-factor-authentication.challenge'))
        ->post(route('auth.two-factor-authentication.confirm'), [
            'code' => '123456',
        ]);

    $response->assertSessionHasErrors(['code']);
    $response->assertSessionMissing(config('tfa-sudo-mode.session_key'));
});

it('throws validation error if code is submitted for a user where two factor authentication is not enabled', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->from(route('auth.two-factor-authentication.challenge'))
        ->post(route('auth.two-factor-authentication.confirm'), [
            'code' => '123456',
        ]);

    $response->assertSessionHasErrors(['code' => __('Two factor authentication is not enabled for this user.')]);
    $response->assertSessionMissing(config('tfa-sudo-mode.session_key'));
});

it('throws validation error if two factor authentication code is not provided', function (): void {
    $user = User::factory()->hasTwoFactorAuthenticationEnabled()->create();

    $response = $this->actingAs($user)
        ->from(route('auth.two-factor-authentication.challenge'))
        ->post(route('auth.two-factor-authentication.confirm'), [
            'code' => null,
        ]);

    $response->assertSessionHasErrors(['code']);
    $response->assertSessionMissing(config('tfa-sudo-mode.session_key'));
});
