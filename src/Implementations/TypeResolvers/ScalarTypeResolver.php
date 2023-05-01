<?php

namespace Zuken\DocsGenerator\Implementations\TypeResolvers;

use Zuken\DocsGenerator\Contracts\PropertyTypeResolverInterface;
use Zuken\DocsGenerator\OpenApiBuilder;
use cebe\openapi\spec\Schema;
use ReflectionNamedType;
use ReflectionProperty;

class ScalarTypeResolver implements PropertyTypeResolverInterface
{
    private const MAP = [
        'int' => 'integer',
        'string' => 'string',
        'float' => 'number',
        'bool' => 'boolean'
    ];

    public function __invoke(ReflectionProperty $property, OpenApiBuilder $context)
    {
        $typeReflection = $property->getType();
        if ($typeReflection instanceof ReflectionNamedType && $scalarType = self::MAP[$typeReflection->getName()] ?? null) {
            return new Schema(['type' => $scalarType]);
        }

        return null;
    }
}
