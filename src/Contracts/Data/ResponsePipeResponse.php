<?php

namespace Zuken\DocsGenerator\Contracts\Data;

use cebe\openapi\spec\Schema;

class ResponsePipeResponse
{
    public Schema $schema;
    public string $schemaName;

    public function __construct(
        Schema $schema,
        string $schemaName
    )
    {
        $this->schemaName = $schemaName;
        $this->schema = $schema;
    }
}
