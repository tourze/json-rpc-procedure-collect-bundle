<?php

declare(strict_types=1);

namespace Tourze\JsonRPCProcedureCollectBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\JsonRPCProcedureCollectBundle\DependencyInjection\JsonRPCProcedureCollectExtension;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(JsonRPCProcedureCollectExtension::class)]
final class JsonRPCProcedureCollectExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    private JsonRPCProcedureCollectExtension $extension;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extension = new JsonRPCProcedureCollectExtension();
    }

    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(JsonRPCProcedureCollectExtension::class, $this->extension);
    }

    public function testGetAlias(): void
    {
        $this->assertEquals('json_rpc_procedure_collect', $this->extension->getAlias());
    }

    public function testExtensionLoadsServices(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'test');

        $this->extension->load([], $container);

        // 验证扩展加载没有抛出异常
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerBuilder', $container);
    }
}
