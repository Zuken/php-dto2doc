<?php

namespace Zuken\DocsGenerator\Implementations\SchemaNameResolver;

use Zuken\DocsGenerator\Attributes\CodegenName;
use Zuken\DocsGenerator\Contracts\SchemaNameResolverInterface;
use ReflectionClass;

class AttributeSchemaNameResolver implements SchemaNameResolverInterface
{
    public function byClass(string $className): ?string
    {
        $attr = (new ReflectionClass($className))->getAttributes(CodegenName::class)[0] ?? null;
        if ($attr) {
            return $attr->newInstance()->name;
        }

        return null;
    }
}
