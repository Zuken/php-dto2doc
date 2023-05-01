<?php

namespace Zuken\DocsGenerator;

use Zuken\DocsGenerator\Contracts\Data\ResponsePipeResponse;
use Zuken\DocsGenerator\Contracts\Data\RouteResponseBag;
use Zuken\DocsGenerator\Contracts\PropertyTypeResolverInterface;
use Zuken\DocsGenerator\Contracts\ResponseGenerationPipeInterface;
use Zuken\DocsGenerator\Contracts\SchemaNameResolverInterface;
use Zuken\DocsGenerator\Implementations\SchemaNameResolver\AttributeSchemaNameResolver;
use Zuken\DocsGenerator\Implementations\SchemaNameResolver\CompositeSchemaNameResolver;
use Zuken\DocsGenerator\Implementations\SchemaNameResolver\DefaultSchemaNameResolver;
use Zuken\DocsGenerator\Implementations\TypeResolvers\ArrayTypeResolver;
use Zuken\DocsGenerator\Implementations\TypeResolvers\DatetimeTypeResolver;
use Zuken\DocsGenerator\Implementations\TypeResolvers\EnumTypeResolver;
use Zuken\DocsGenerator\Implementations\TypeResolvers\ScalarTypeResolver;
use Zuken\DocsGenerator\Implementations\TypeResolvers\SubclassTypeResolver;
use cebe\openapi\spec\Components;
use cebe\openapi\spec\OpenApi;
use cebe\openapi\spec\Operation;
use cebe\openapi\spec\Parameter;
use cebe\openapi\spec\PathItem;
use cebe\openapi\spec\Reference;
use cebe\openapi\spec\RequestBody;
use cebe\openapi\spec\Response;
use cebe\openapi\spec\Responses;
use cebe\openapi\spec\Schema;
use cebe\openapi\Writer;
use ReflectionClass;

class OpenApiBuilder
{
    private OpenApi $api;

    private array $responsePipes = [];
    private ClassPropertyResolver $propertyResolver;
    private ?SchemaNameResolverInterface $schemaNameResolver;

    public function __construct(
        ClassPropertyResolver $propertyResolver,
        ?SchemaNameResolverInterface $schemaNameResolver = null
    ) {
        $this->schemaNameResolver = $schemaNameResolver;
        $this->propertyResolver = $propertyResolver;
        $this->api = new OpenApi([
            'openapi' => '3.0.2',
            'info' => [
                'title' => 'Test API',
                'version' => '1.0.0',
            ],
            'paths' => [],
            'components' => new Components([
                'schemas' => []
            ])
        ]);

        if (!$propertyResolver->getResolvers()) {
            $this->propertyResolver->addResolver(new ScalarTypeResolver());
            $this->propertyResolver->addResolver(new ArrayTypeResolver());
            $this->propertyResolver->addResolver(new DatetimeTypeResolver());
            $this->propertyResolver->addResolver(new EnumTypeResolver());
            $this->propertyResolver->addResolver(new SubclassTypeResolver());
        }

        if (!$this->schemaNameResolver) {
            $this->schemaNameResolver = new CompositeSchemaNameResolver([
                new AttributeSchemaNameResolver(),
                new DefaultSchemaNameResolver()
            ]);
        }
    }
    public function addPropertyResolver(PropertyTypeResolverInterface $resolver, int $priority = 100)
    {
        $this->propertyResolver->addResolver($resolver, $priority);

    }

    public function generateSchemaNameByClass(string $class): string
    {
        return $this->schemaNameResolver->byClass($class);
    }

    public function addResponsePipe(ResponseGenerationPipeInterface $pipe): void
    {
        $this->responsePipes[] = $pipe;
    }

    public function addResponse(
        PathItem         $pathItem,
        RouteResponseBag $bag,
        $contentType = 'application/json'
    ): void
    {
        $method = strtolower($bag->method);

        $schema = new Schema([
            'type' => 'object',
            'properties' => []
        ]);

        $pipeResult = new ResponsePipeResponse(
            $schema,
            $this->generateSchemaNameByClass($bag->responseClass) . '.Response'
        );
        /** @var ResponseGenerationPipeInterface $pipe */
        foreach ($this->responsePipes as $pipe) {
            $pipeResult = $pipe->handle($pipeResult, $bag, $this);
        }

        $ref = $this->registerSchemaIfNotExists($pipeResult->schemaName, $pipeResult->schema);

        $operation = $pathItem->$method ?: $this->createEmptyOperation();
        $operation->responses->addResponse($bag->code, new Response([
            'description' => '',
            'content' => [
                $contentType => [
                    'schema' => $ref
                ],
            ]
        ]));
        $pathItem->$method = $operation;
    }

    private function createEmptyOperation(): Operation
    {
        return new Operation([
            'responses' => new Responses([])
        ]);
    }

    public function addRequest(
        PathItem $pathItem,
        string   $uri,
        string   $method,
        string   $requestClass,
        $contentType = 'application/json'
    ): void {
        $method = strtolower($method);
        $ref = $this->getSchema(
            $this->generateSchemaNameByClass($requestClass),
            new ReflectionClass($requestClass)
        );

        $operation = $pathItem->$method ?: $this->createEmptyOperation();

        $parameters = $operation->parameters;
        if (preg_match_all('/{(.+?)}/', $uri, $m)) {
            foreach ($m[1] as $pathString) {
                $parameters[] = new Parameter([
                    'name' => $pathString,
                    'in' => 'path',
                    'required' => true,
                    'schema' => ['type' => 'string']
                ]);
            }
        }

        $operation->parameters = $parameters;
        if ($method !== 'get') {
            $operation->requestBody = new RequestBody([
                'content' => [
                    $contentType => ['schema' => $ref]
                ]
            ]);
            //todo: get filters
        }

        $pathItem->$method = $operation;
    }
    public function addRoute(string $route)
    {

        if (! $pathItem = $this->api->paths->getPath($route)) {
            $this->api->paths->addPath($route, $pathItem = new PathItem([]));
        }
        return $pathItem;

    }

    public function writeToFile(string $filePath): void
    {
        Writer::writeToJsonFile($this->api, $filePath);
    }


    public function getSchema(string $schemaName, ReflectionClass $reflectionClass): Reference
    {
        $schema = new Schema([
            'type' => 'object',
            'properties' => []
        ]);

        $properties = $schema->properties;
        foreach ($reflectionClass->getProperties() as $property) {
            $properties[$property->getName()] = $this->propertyResolver->resolve($property, $this);
        }

        $schema->properties = $properties;

        $reference = $this->registerSchemaIfNotExists($schemaName, $schema);

        return $reference;
    }

    public function registerSchemaIfNotExists(string $schemaName, Schema $schema): Reference
    {
        $schemas = $this->api->components->schemas;
        $schemas[$schemaName] = $schema;

        $this->api->components->schemas = $schemas;

        return new Reference(['$ref' => '#/components/schemas/' . $schemaName]);
    }

    public function setNameResolver(CompositeSchemaNameResolver $nameResolver): void
    {
        $this->schemaNameResolver = $nameResolver;
    }
}
