<?php

namespace Zuken\DocsGenerator\Contracts;

use Zuken\DocsGenerator\Contracts\Data\ResponsePipeResponse;
use Zuken\DocsGenerator\Contracts\Data\RouteResponseBag;
use Zuken\DocsGenerator\OpenApiBuilder;

interface ResponseGenerationPipeInterface
{
    public function handle(
        ResponsePipeResponse $pipeResponse,
        RouteResponseBag $bag,
        OpenApiBuilder $context
    ): ResponsePipeResponse;
}
