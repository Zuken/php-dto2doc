<?php

namespace Zuken\DocsGenerator;

use Zuken\DocsGenerator\Contracts\PropertyTypeResolverInterface;
use cebe\openapi\spec\Reference;
use cebe\openapi\spec\Schema;
use ReflectionProperty;

class ClassPropertyResolver
{
    private array $resolvers = [];

    public function getResolvers(): array
    {
        return $this->resolvers;
    }
    public function addResolver(PropertyTypeResolverInterface $propertyTypeResolver, int $priority = 100)
    {
        $this->resolvers[$priority][] = $propertyTypeResolver;

        ksort($this->resolvers);
    }

    /**
     * @param ReflectionProperty $property
     * @param OpenApiBuilder $builder
     * @return Reference|Schema|null
     */
    public function resolve(ReflectionProperty $property, OpenApiBuilder $builder)
    {
        /** @var PropertyTypeResolverInterface $resolver */
        foreach ($this->resolvers as $priority => $resolvers) {
            foreach ($resolvers as $resolver) {
                if ($schema = $resolver($property, $builder)) {
                    return $schema;
                }
            }
        }

        return null;
    }
}
