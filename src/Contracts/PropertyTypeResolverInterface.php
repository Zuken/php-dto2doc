<?php

namespace Zuken\DocsGenerator\Contracts;

use Zuken\DocsGenerator\OpenApiBuilder;
use cebe\openapi\spec\Reference;
use cebe\openapi\spec\Schema;
use ReflectionProperty;

interface PropertyTypeResolverInterface
{
    /**
     * @param ReflectionProperty $property
     * @return Schema|Reference|null
     */
    public function __invoke(ReflectionProperty $property, OpenApiBuilder $context);
}
