<?php

namespace App\Tests\Unit\OpenApi;

use ApiPlatform\Core\JsonSchema\Schema;
use ApiPlatform\Core\JsonSchema\SchemaFactoryInterface;
use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Components;
use ApiPlatform\Core\OpenApi\Model\Info;
use ApiPlatform\Core\OpenApi\Model\Paths;
use ApiPlatform\Core\OpenApi\OpenApi;
use App\Controller\SecurityController;
use App\OpenApi\LoginActionDoc;
use App\OpenApi\OpenApiFactory;
use Codeception\Test\Unit;

class FactoryTest extends Unit
{
    public function testInvoke()
    {
        $openApiFactory = $this->createMock(OpenApiFactoryInterface::class);
        $openApi = new OpenApi(new Info('Test', '3.0.3'), [], new Paths(), components: new Components());
        $openApiFactory->method('__invoke')->willReturn($openApi);

        $schemaFactory = $this->createMock(SchemaFactoryInterface::class);
        $schemaFactory->method('buildSchema')->willReturn(new Schema());

        $decorator = new OpenApiFactory($openApiFactory, $schemaFactory, [new LoginActionDoc()]);
        $openApiWithNewSchemas = $decorator();
        $paths = $openApiWithNewSchemas->getPaths();
        $this->assertArrayHasKey(SecurityController::ROUTE_LOGIN, $paths->getPaths());
    }
}
