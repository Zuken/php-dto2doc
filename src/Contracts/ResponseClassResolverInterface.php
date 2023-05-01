<?php

namespace Zuken\DocsGenerator\Contracts;

use ReflectionMethod;

interface ResponseClassResolverInterface
{
    public function getResponseClasses(ReflectionMethod $reflectionMethod): array;
}
