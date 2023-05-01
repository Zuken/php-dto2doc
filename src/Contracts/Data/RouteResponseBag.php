<?php

namespace Zuken\DocsGenerator\Contracts\Data;

class RouteResponseBag
{
    public string $responseClass;
    public string $method;
    public int $code = 200;
    public array $extras = [];

    public function __construct(
        string $responseClass,
        string $method,
        int $code = 200,
        array $extras = []
    )
    {
        $this->extras = $extras;
        $this->code = $code;
        $this->method = $method;
        $this->responseClass = $responseClass;
    }

    public function getExtra(string $key)
    {
        return $this->extras[$key] ?? null;
    }
}
