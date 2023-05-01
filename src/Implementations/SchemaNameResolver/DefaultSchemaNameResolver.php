<?php

namespace Zuken\DocsGenerator\Implementations\SchemaNameResolver;

use Zuken\DocsGenerator\Contracts\SchemaNameResolverInterface;

class DefaultSchemaNameResolver implements SchemaNameResolverInterface
{
    public function byClass(string $className): ?string
    {
        return str_replace('\\', '.', $className);
    }
}
