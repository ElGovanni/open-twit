<?php

namespace App\OpenApi;

use ApiPlatform\Core\JsonSchema\Schema;
use ApiPlatform\Core\JsonSchema\SchemaFactoryInterface;
use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\MediaType;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\Model\Response;
use ApiPlatform\Core\OpenApi\OpenApi;
use ArrayObject;
use InvalidArgumentException;
use ReflectionClass;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated,
        private SchemaFactoryInterface $schemaFactory,
        private iterable $taggedClass
    ) {}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        $path = [];

        foreach ($this->taggedClass as $class) {
            $requestBody = null;

            $reflection = new ReflectionClass($class);
            $classAttributes = $reflection->getAttributes();
            /** @var Doc $arguments */
            $arguments = $classAttributes[0]->newInstance();

            if ($class instanceof RequestInterface) {
                $request = $this->addSchema($class->getRequestClass(), $openApi);
                $requestBody = $this->createRequestBody($request, $arguments->contentType);
            }
            $operation = new Operation(uniqid(), tags: [$arguments->tag], requestBody: $requestBody);

            if ($class instanceof ResponseInterface) {
                $responseSchema = $this->addSchema($class->getResponseClass(), $openApi);
                $responseSchemaAsArray = new ArrayObject($responseSchema->getArrayCopy(true));
                $responseContent = new ArrayObject([
                    $arguments->contentType => new MediaType($responseSchemaAsArray),
                ]);
                $response = new Response(content: $responseContent);
                $operation->addResponse($response, $arguments->responseCode);
            }

            $method = 'with' . ucfirst(strtolower($arguments->method));

            $url = $arguments->url;
            $pathItem = $path[$url] ?? new PathItem();
            $pathItem = $pathItem->{$method}($operation);

            $path[$url] = $pathItem;
        }

        foreach ($path as $key => $item) {
            $openApi->getPaths()->addPath($key, $item);
        }

        return $openApi;
    }

    private function addSchema(string $className, OpenApi $openApi, array $serializerGroups = []): Schema
    {
        if (! class_exists($className)) {
            throw new InvalidArgumentException(sprintf('Class %s does not exists', $className));
        }

        $serializerContext = null;
        if (sizeof($serializerGroups)) {
            $serializerContext = [
                AbstractNormalizer::GROUPS => $serializerGroups,
            ];
        }

        return $this->formatSchema($className, $serializerContext, $openApi);
    }

    private function formatSchema(string $className, ?array $serializerContext, OpenApi $openApi): Schema
    {
        $schema = $this->schemaFactory->buildSchema(
            $className,
            format: 'json',
            schema: new Schema(Schema::VERSION_OPENAPI),
            serializerContext: $serializerContext
        );

        $arrayCopy = $schema->getDefinitions()->getArrayCopy();
        $key = key($arrayCopy);
        $schemas = $openApi->getComponents()->getSchemas();

        if ($schemas !== null) {
            $schemas->offsetSet($key, $arrayCopy[$key]);
        }

        return $schema;
    }

    private function createRequestBody(Schema $schemaRefreshRequest, string $contentType): RequestBody
    {
        $arrayObject = new ArrayObject($schemaRefreshRequest->getArrayCopy(true));
        return new RequestBody(content: new ArrayObject([
            $contentType => new MediaType($arrayObject),
        ]), required: true);
    }
}
