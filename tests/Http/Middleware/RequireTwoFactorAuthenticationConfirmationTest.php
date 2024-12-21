<?php

use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Wnx\TfaConfirmation\Http\Middleware\RequireTwoFactorAuthenticationConfirmation;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\withSession;

beforeEach(function (): void {
    Route::get('_/require-two-factor', fn () => response()->json([
        'success' => true,
    ]))->middleware([
        StartSession::class,
        RequireTwoFactorAuthenticationConfirmation::class,
    ]);

    Route::post('_/require-two-factor-post', fn () => response()->json([
        'success' => true,
    ]))->middleware([
        StartSession::class,
        RequireTwoFactorAuthenticationConfirmation::class,
    ]);
});

it('redirects user to confirmation route if two factor code is required', function (): void {
    $user = User::factory()->hasTwoFactorAuthenticationEnabled()->create();

    actingAs($user)
        ->get('_/require-two-factor')
        ->assertRedirect(route('auth.two-factor-authentication.challenge'));
});

it('returns json response if two factor code is required', function (): void {
    $user = User::factory()->hasTwoFactorAuthenticationEnabled()->create();

    actingAs($user)
        ->getJson('_/require-two-factor')
        ->assertJson([
            'message' => __('tfa-confirmation::translations.responses.json'),
        ]);
});

it('does not redirect user if two factor code is not required as user has confirmed code before', function (): void {
    $user = User::factory()->hasTwoFactorAuthenticationEnabled()->create();

    withSession(['auth.two_factor_confirmed_at' => time()]);

    actingAs($user)
        ->get('_/require-two-factor')
        ->assertJson([
            'success' => true,
        ]);
});

it('does not redirect user if two factor code is not required for POST as user has confirmed code before', function (): void {
    $user = User::factory()->hasTwoFactorAuthenticationEnabled()->create();

    withSession(['auth.two_factor_confirmed_at' => time()]);

    actingAs($user)
        ->post('_/require-two-factor-post')
        ->assertJson([
            'success' => true,
        ]);
});

it('does not redirect user if tfa-confirmation has been disabled', function () {
    $user = User::factory()->hasTwoFactorAuthenticationEnabled()->create();

    config(['tfa-confirmation.enabled' => false]);

    actingAs($user)
        ->get('_/require-two-factor')
        ->assertJson([
            'success' => true,
        ]);
});

it('does not redirect user if two factor authentication is not enabled', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get('_/require-two-factor')
        ->assertJson([
            'success' => true,
        ]);
});

it('redirects user to confirmation route if two factor confirmation has timed out', function (): void {
    $user = User::factory()->hasTwoFactorAuthenticationEnabled()->create();

    withSession(['auth.two_factor_confirmed_at' => now()->subDays(7)->timestamp]);

    actingAs($user)
        ->get('_/require-two-factor')
        ->assertRedirect(route('auth.two-factor-authentication.challenge'));
});
