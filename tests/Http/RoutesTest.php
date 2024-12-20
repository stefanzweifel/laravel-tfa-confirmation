<?php

use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('returns challenge view from challenge route', function () {
    $user = User::factory()->hasTwoFactorAuthenticationEnabled()->create();

    $response = actingAs($user)
        ->get(route('auth.two-factor-authentication.challenge'));

    $response->assertOk();
    $response->assertViewIs('tfa-sudo-mode::challenge');
});

it('redirects to root if user without two factor authentication visits challenge view', function () {
    $user = User::factory()->create();

    $response = actingAs($user)
        ->get(route('auth.two-factor-authentication.challenge'));

    $response->assertRedirect('/');
});

it('redirects to login route if guests visits confirm route', function () {
    $response = get(route('auth.two-factor-authentication.challenge'));

    $response->assertRedirect('/login');
});

it('redirects to challenge view if user has not confirmed two factor authentication within timeout', function () {
    $user = User::factory()->hasTwoFactorAuthenticationEnabled()->create();

    $response = actingAs($user)
        ->get(route('auth.two-factor-authentication.challenge'));

    $response->assertOk();
    $response->assertViewIs('tfa-sudo-mode::challenge');
});
