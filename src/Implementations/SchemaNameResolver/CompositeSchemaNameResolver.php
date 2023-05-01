<?php

namespace Zuken\DocsGenerator\Implementations\SchemaNameResolver;

use Zuken\DocsGenerator\Contracts\SchemaNameResolverInterface;

class CompositeSchemaNameResolver implements SchemaNameResolverInterface
{
    private array $namePerClassMap = [];
    private array $chain = [];

    public function __construct(
        array $chain = []
    )
    {
        $this->chain = $chain;
    }

    public function byClass(string $className): ?string
    {
        foreach ($this->chain as $resolver) {
            if (!$resolver instanceof SchemaNameResolverInterface) {
                throw new \RuntimeException();
            }

            if ($name = $resolver->byClass($className)) {
                $this->checkForNameCollisions($name, $className);

                return $name;
            }
        }

        return null;
    }

    private function checkForNameCollisions(string $name, string $className): void
    {
        $exisingClassName = $this->namePerClassMap[$name] ?? $className;

        if ($exisingClassName !== $className) {
            throw new \RuntimeException(sprintf(
                'Cannot assign to "%s" name "%s" - it is already token by "%s"',
                $className,
                $name,
                $exisingClassName
            ));
        }

        $this->namePerClassMap[$name] = $className;
    }
}
