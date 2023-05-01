<?php

namespace Zuken\DocsGenerator\Implementations\TypeResolvers;

use Zuken\DocsGenerator\Contracts\PropertyTypeResolverInterface;
use Zuken\DocsGenerator\OpenApiBuilder;
use BackedEnum;
use cebe\openapi\spec\Schema;
use ReflectionProperty;

class EnumTypeResolver implements PropertyTypeResolverInterface
{
    public function __invoke(ReflectionProperty $property, OpenApiBuilder $context)
    {
        if (!function_exists('enum_exists') || !class_exists('BackedEnum')) {
            return null;
        }

        $typeReflectionName = $property->getType()->getName();

        if (enum_exists($typeReflectionName) && (class_implements($typeReflectionName)[BackedEnum::class] ?? false)) {
            /** @var BackedEnum $typeReflectionName */
            $schema = new Schema([
                'enum' => array_map(fn(BackedEnum $enum) => $enum->value, $typeReflectionName::cases()),
                'x-enum-varnames' => array_map(fn(BackedEnum $enum) => $enum->name, $typeReflectionName::cases())
            ]);

            $enumSchemaName = $context->generateSchemaNameByClass($typeReflectionName) . 'Enum';
            $ref = $context->registerSchemaIfNotExists($enumSchemaName, $schema);

            return $ref;
        }
    }
}
