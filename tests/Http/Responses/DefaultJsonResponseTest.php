<?php

use Wnx\TfaConfirmation\Http\Responses\DefaultJsonResponse;

it('returns a json response with a message and status code 423', function () {
    $response = app(DefaultJsonResponse::class)();

    expect($response->status())->toBe(423);

    expect(json_decode($response->content(), true))->toBe([
        'message' => __('tfa-sudo-mode::translations.responses.json'),
    ]);
});
