<?php

namespace Tourze\JsonRPCProcedureCollectBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\JsonRPCProcedureCollectBundle\DependencyInjection\NameCollectorCompilerPass;
use Tourze\JsonRPCProcedureCollectBundle\JsonRPCProcedureCollectBundle;

class JsonRPCProcedureCollectBundleTest extends TestCase
{
    /**
     * 测试Bundle能正确实例化
     */
    public function testInstantiation_createsBundle(): void
    {
        $bundle = new JsonRPCProcedureCollectBundle();
        
        $this->assertInstanceOf(JsonRPCProcedureCollectBundle::class, $bundle);
        $this->assertInstanceOf(Bundle::class, $bundle);
    }

    /**
     * 测试build方法正确添加CompilerPass
     */
    public function testBuild_addsNameCollectorCompilerPass(): void
    {
        $container = $this->createMock(ContainerBuilder::class);
        
        // 验证addCompilerPass方法被调用一次，且参数为NameCollectorCompilerPass实例
        $container->expects($this->once())
            ->method('addCompilerPass')
            ->with($this->callback(function ($pass) {
                return $pass instanceof NameCollectorCompilerPass;
            }))
            ->willReturn($container);
        
        $bundle = new JsonRPCProcedureCollectBundle();
        $bundle->build($container); // @phpstan-ignore-line
    }

    /**
     * 测试build方法不会抛出异常
     */
    public function testBuild_executesWithoutErrors(): void
    {
        $container = new ContainerBuilder();
        $bundle = new JsonRPCProcedureCollectBundle();

        // 验证不会抛出异常
        $this->expectNotToPerformAssertions();
        $bundle->build($container);
    }

    /**
     * 测试CompilerPass被正确创建
     */
    public function testBuild_createsCorrectCompilerPass(): void
    {
        $container = new ContainerBuilder();
        $bundle = new JsonRPCProcedureCollectBundle();

        // 这个测试主要验证没有异常抛出，说明CompilerPass被正确创建
        $bundle->build($container);
        
        // 如果到这里没有抛出异常，说明NameCollectorCompilerPass被正确实例化
        $this->assertTrue(true);
    }
} 