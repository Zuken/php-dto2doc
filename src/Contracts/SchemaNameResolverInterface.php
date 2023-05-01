<?php

namespace Zuken\DocsGenerator\Contracts;

interface SchemaNameResolverInterface
{
    public function byClass(string $className): ?string;
}
