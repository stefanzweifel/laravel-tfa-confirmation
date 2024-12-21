<?php

namespace Wnx\TfaConfirmation\Http\Responses;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;

class DefaultJsonResponse
{
    public function __construct(
        protected ResponseFactory $responseFactory
    ) {}

    public function __invoke(): JsonResponse
    {
        return $this->responseFactory->json([
            'message' => __('tfa-confirmation::translations.responses.json'),
        ], 423);
    }
}
