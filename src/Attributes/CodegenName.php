<?php

namespace Zuken\DocsGenerator\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class CodegenName
{
    public function __construct(
        public string $name
    )
    {
    }
}
