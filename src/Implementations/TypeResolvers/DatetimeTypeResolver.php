<?php

namespace Zuken\DocsGenerator\Implementations\TypeResolvers;

use Zuken\DocsGenerator\Contracts\PropertyTypeResolverInterface;
use Zuken\DocsGenerator\OpenApiBuilder;
use cebe\openapi\spec\Schema;
use DateTimeInterface;
use ReflectionProperty;

class DatetimeTypeResolver implements PropertyTypeResolverInterface
{
    public function __invoke(ReflectionProperty $property, OpenApiBuilder $context)
    {
        $classCandidate = $property->getType()->getName();
        if (class_exists($classCandidate) && (class_implements($classCandidate)[DateTimeInterface::class] ?? false)) {
            return new Schema(['type' => 'string', 'format' => 'date-time']);
        }

        return null;
    }
}
