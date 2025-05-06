<?php

namespace Tourze\JsonRPCProcedureCollectBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\JsonRPCProcedureCollectBundle\DependencyInjection\JsonRPCProcedureCollectExtension;
use Tourze\JsonRPCProcedureCollectBundle\Service\NameCollector;

class JsonRPCProcedureCollectExtensionTest extends TestCase
{
    /**
     * 测试Extension.load方法能正确加载服务
     */
    public function testLoad_registersServices(): void
    {
        // 创建容器构建器
        $container = new ContainerBuilder();

        // 创建Extension实例
        $extension = new JsonRPCProcedureCollectExtension();

        // 执行加载方法
        $extension->load([], $container);

        // 验证NameCollector服务是否已注册
        $this->assertTrue($container->hasDefinition(NameCollector::class));

        // 获取服务定义并验证其属性
        $nameCollectorDefinition = $container->getDefinition(NameCollector::class);

        // 验证服务定义是自动装配的
        $this->assertTrue($nameCollectorDefinition->isAutowired());

        // 验证服务定义是自动配置的
        $this->assertTrue($nameCollectorDefinition->isAutoconfigured());
    }
}
