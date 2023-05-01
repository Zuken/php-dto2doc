<?php

namespace Zuken\DocsGenerator\Implementations\TypeResolvers;

use Zuken\DocsGenerator\Contracts\PropertyTypeResolverInterface;
use Zuken\DocsGenerator\OpenApiBuilder;
use ReflectionClass;
use ReflectionProperty;

class SubclassTypeResolver implements PropertyTypeResolverInterface
{
    public function __invoke(ReflectionProperty $property, OpenApiBuilder $context)
    {
        $typeReflection = $property->getType();

        if (class_exists($typeReflection->getName())) {
            return $context->getSchema(
                $context->generateSchemaNameByClass($typeReflection->getName()),
                new ReflectionClass($typeReflection->getName())
            );
        }

        return null;
    }
}
