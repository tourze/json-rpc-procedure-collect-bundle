<?php

namespace Tourze\JsonRPCProcedureCollectBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Domain\JsonRpcMethodInterface;
use Tourze\JsonRPCProcedureCollectBundle\DependencyInjection\NameCollectorCompilerPass;
use Tourze\JsonRPCProcedureCollectBundle\Service\NameCollector;

/**
 * 用于测试的模拟类实现JsonRpcMethodInterface接口
 */
class MockJsonRpcMethod implements JsonRpcMethodInterface
{
    public function __invoke($request): mixed
    {
        return [];
    }

    public function execute(): array
    {
        return [];
    }
}

/**
 * 用于测试的不实现JsonRpcMethodInterface接口的类
 */
class InvalidMockClass
{
}

class NameCollectorCompilerPassTest extends TestCase
{
    /**
     * 测试处理有效的服务
     */
    public function testProcess_withValidServices(): void
    {
        // 创建容器构建器
        $container = new ContainerBuilder();

        // 创建NameCollector模拟定义
        $nameCollectorDef = new Definition(NameCollector::class);
        $container->setDefinition(NameCollector::class, $nameCollectorDef);

        // 创建标记为JSON-RPC方法的服务定义
        $methodName = 'testMethod';
        $serviceDef = new Definition(MockJsonRpcMethod::class);
        $serviceId = 'test.jsonrpc.method';
        $container->setDefinition($serviceId, $serviceDef);

        // 添加方法标记
        $container->findDefinition($serviceId)
            ->addTag(MethodExpose::JSONRPC_METHOD_TAG, [NameCollectorCompilerPass::JSONRPC_METHOD_TAG_METHOD_NAME_KEY => $methodName]);

        // 创建并执行CompilerPass
        $compilerPass = new NameCollectorCompilerPass();
        $compilerPass->process($container);

        // 验证NameCollector的addProcedure方法被调用了一次
        $methodCalls = $nameCollectorDef->getMethodCalls();
        $this->assertCount(1, $methodCalls);

        $addProcedureCalls = array_filter($methodCalls, function ($call) {
            return $call[0] === 'addProcedure';
        });

        $this->assertCount(1, $addProcedureCalls);

        $methodCall = reset($addProcedureCalls);
        $this->assertEquals('addProcedure', $methodCall[0]);
        $this->assertEquals($methodName, $methodCall[1][0]);
        $this->assertEquals(MockJsonRpcMethod::class, $methodCall[1][1]);
    }

    /**
     * 测试处理缺少method标签属性的服务时抛出异常
     */
    public function testProcess_withInvalidTag(): void
    {
        // 创建容器构建器
        $container = new ContainerBuilder();

        // 创建NameCollector模拟定义
        $nameCollectorDef = new Definition(NameCollector::class);
        $container->setDefinition(NameCollector::class, $nameCollectorDef);

        // 创建标记为JSON-RPC方法但缺少method属性的服务定义
        $serviceDef = new Definition(MockJsonRpcMethod::class);
        $serviceId = 'test.jsonrpc.method.invalid_tag';
        $container->setDefinition($serviceId, $serviceDef);

        // 添加缺少method属性的标记
        $container->findDefinition($serviceId)
            ->addTag(MethodExpose::JSONRPC_METHOD_TAG, []);

        // 创建CompilerPass
        $compilerPass = new NameCollectorCompilerPass();

        // 预期抛出LogicException异常
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(sprintf(
            'Service "%s" is taggued as JSON-RPC method but does not have method name defined under "%s" tag attribute key',
            $serviceId,
            NameCollectorCompilerPass::JSONRPC_METHOD_TAG_METHOD_NAME_KEY
        ));

        // 执行CompilerPass
        $compilerPass->process($container);
    }

    /**
     * 测试处理未实现JsonRpcMethodInterface接口的服务时抛出异常
     */
    public function testProcess_withInvalidInterface(): void
    {
        // 创建容器构建器
        $container = new ContainerBuilder();

        // 创建NameCollector模拟定义
        $nameCollectorDef = new Definition(NameCollector::class);
        $container->setDefinition(NameCollector::class, $nameCollectorDef);

        // 创建标记为JSON-RPC方法但未实现接口的服务定义
        $methodName = 'testMethod';
        $serviceDef = new Definition(InvalidMockClass::class);
        $serviceId = 'test.jsonrpc.method.invalid_interface';
        $container->setDefinition($serviceId, $serviceDef);

        // 添加方法标记
        $container->findDefinition($serviceId)
            ->addTag(MethodExpose::JSONRPC_METHOD_TAG, [NameCollectorCompilerPass::JSONRPC_METHOD_TAG_METHOD_NAME_KEY => $methodName]);

        // 创建CompilerPass
        $compilerPass = new NameCollectorCompilerPass();

        // 预期抛出LogicException异常
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(sprintf(
            'Service "%s" is taggued as JSON-RPC method but does not implement %s',
            $serviceId,
            JsonRpcMethodInterface::class
        ));

        // 执行CompilerPass
        $compilerPass->process($container);
    }

    /**
     * 测试处理多个标签的服务
     */
    public function testProcess_withMultipleTags(): void
    {
        // 创建容器构建器
        $container = new ContainerBuilder();

        // 创建NameCollector模拟定义
        $nameCollectorDef = new Definition(NameCollector::class);
        $container->setDefinition(NameCollector::class, $nameCollectorDef);

        // 创建标记为JSON-RPC方法的服务定义
        $serviceDef = new Definition(MockJsonRpcMethod::class);
        $serviceId = 'test.jsonrpc.method.multiple_tags';
        $container->setDefinition($serviceId, $serviceDef);

        // 添加多个方法标记
        $container->findDefinition($serviceId)
            ->addTag(MethodExpose::JSONRPC_METHOD_TAG, [NameCollectorCompilerPass::JSONRPC_METHOD_TAG_METHOD_NAME_KEY => 'method1'])
            ->addTag(MethodExpose::JSONRPC_METHOD_TAG, [NameCollectorCompilerPass::JSONRPC_METHOD_TAG_METHOD_NAME_KEY => 'method2']);

        // 创建并执行CompilerPass
        $compilerPass = new NameCollectorCompilerPass();
        $compilerPass->process($container);

        // 验证NameCollector的addProcedure方法被调用了两次
        $methodCalls = $nameCollectorDef->getMethodCalls();
        $this->assertCount(2, $methodCalls);

        $addProcedureCalls = array_filter($methodCalls, function ($call) {
            return $call[0] === 'addProcedure';
        });

        $this->assertCount(2, $addProcedureCalls);
    }

    /**
     * 测试处理多个服务
     */
    public function testProcess_withMultipleServices(): void
    {
        // 创建容器构建器
        $container = new ContainerBuilder();

        // 创建NameCollector模拟定义
        $nameCollectorDef = new Definition(NameCollector::class);
        $container->setDefinition(NameCollector::class, $nameCollectorDef);

        // 创建第一个标记为JSON-RPC方法的服务定义
        $serviceDef1 = new Definition(MockJsonRpcMethod::class);
        $serviceId1 = 'test.jsonrpc.method1';
        $container->setDefinition($serviceId1, $serviceDef1);
        $container->findDefinition($serviceId1)
            ->addTag(MethodExpose::JSONRPC_METHOD_TAG, [NameCollectorCompilerPass::JSONRPC_METHOD_TAG_METHOD_NAME_KEY => 'method1']);

        // 创建第二个标记为JSON-RPC方法的服务定义
        $serviceDef2 = new Definition(MockJsonRpcMethod::class);
        $serviceId2 = 'test.jsonrpc.method2';
        $container->setDefinition($serviceId2, $serviceDef2);
        $container->findDefinition($serviceId2)
            ->addTag(MethodExpose::JSONRPC_METHOD_TAG, [NameCollectorCompilerPass::JSONRPC_METHOD_TAG_METHOD_NAME_KEY => 'method2']);

        // 创建并执行CompilerPass
        $compilerPass = new NameCollectorCompilerPass();
        $compilerPass->process($container);

        // 验证NameCollector的addProcedure方法被调用了两次
        $methodCalls = $nameCollectorDef->getMethodCalls();
        $this->assertCount(2, $methodCalls);

        $addProcedureCalls = array_filter($methodCalls, function ($call) {
            return $call[0] === 'addProcedure';
        });

        $this->assertCount(2, $addProcedureCalls);
    }

    /**
     * 测试处理空容器（没有标记的服务）
     */
    public function testProcess_withEmptyContainer(): void
    {
        // 创建容器构建器
        $container = new ContainerBuilder();

        // 创建NameCollector模拟定义
        $nameCollectorDef = new Definition(NameCollector::class);
        $container->setDefinition(NameCollector::class, $nameCollectorDef);

        // 创建并执行CompilerPass
        $compilerPass = new NameCollectorCompilerPass();
        $compilerPass->process($container);

        // 验证NameCollector没有被调用任何方法
        $methodCalls = $nameCollectorDef->getMethodCalls();
        $this->assertEmpty($methodCalls);
    }

    /**
     * 测试处理没有NameCollector定义的容器
     */
    public function testProcess_withoutNameCollectorDefinition(): void
    {
        // 创建容器构建器（但不添加NameCollector定义）
        $container = new ContainerBuilder();

        // 创建标记为JSON-RPC方法的服务定义
        $serviceDef = new Definition(MockJsonRpcMethod::class);
        $serviceId = 'test.jsonrpc.method';
        $container->setDefinition($serviceId, $serviceDef);
        $container->findDefinition($serviceId)
            ->addTag(MethodExpose::JSONRPC_METHOD_TAG, [NameCollectorCompilerPass::JSONRPC_METHOD_TAG_METHOD_NAME_KEY => 'testMethod']);

        // 创建并执行CompilerPass
        $compilerPass = new NameCollectorCompilerPass();

        // 预期抛出ServiceNotFoundException异常，因为NameCollector定义不存在
        $this->expectException(\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException::class);
        $this->expectExceptionMessage('You have requested a non-existent service "Tourze\JsonRPCProcedureCollectBundle\Service\NameCollector"');

        $compilerPass->process($container);
    }
}
