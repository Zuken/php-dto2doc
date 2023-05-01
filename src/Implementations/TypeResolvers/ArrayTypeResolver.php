<?php

namespace Zuken\DocsGenerator\Implementations\TypeResolvers;

use Zuken\DocsGenerator\Contracts\PropertyTypeResolverInterface;
use Zuken\DocsGenerator\OpenApiBuilder;
use cebe\openapi\spec\Schema;
use ReflectionProperty;

class ArrayTypeResolver implements PropertyTypeResolverInterface
{
    public function __invoke(ReflectionProperty $property, OpenApiBuilder $context)
    {
        $typeReflection = $property->getType();

        if ($typeReflection->getName() === 'array') {
            return new Schema(['type' => 'array']);
        }
    }
}
