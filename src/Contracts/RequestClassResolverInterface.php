<?php

namespace Zuken\DocsGenerator\Contracts;

use ReflectionMethod;

interface RequestClassResolverInterface
{
    public function getRequestClass(ReflectionMethod $reflectionMethod): ?string;
}
