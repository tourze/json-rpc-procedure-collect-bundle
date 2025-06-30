<?php

namespace Tourze\JsonRPCProcedureCollectBundle\Tests\Procedure;

use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPCProcedureCollectBundle\Procedure\GetProcedureList;
use Tourze\JsonRPCProcedureCollectBundle\Service\NameCollector;

class GetProcedureListTest extends TestCase
{
    /**
     * 测试当NameCollector为空时，execute方法返回空数组
     */
    public function testExecute_emptyCollector(): void
    {
        // 创建NameCollector模拟对象
        $collector = $this->createMock(NameCollector::class);

        // 配置模拟对象的getProcedures方法返回空数组
        $collector->method('getProcedures')
            ->willReturn([]);

        // 创建被测试对象，注入模拟的NameCollector
        $procedureList = new GetProcedureList($collector);

        // 执行方法并验证结果
        $result = $procedureList->execute();
        $this->assertEmpty($result);
    }

    /**
     * 测试当NameCollector包含一个过程时，execute方法返回正确的数组
     */
    public function testExecute_withOneProcedure(): void
    {
        // 要返回的过程数据
        $procedureData = [
            'testMethod' => 'TestClass'
        ];

        // 创建NameCollector模拟对象
        $collector = $this->createMock(NameCollector::class);

        // 配置模拟对象的getProcedures方法返回一个过程
        $collector->method('getProcedures')
            ->willReturn($procedureData);

        // 创建被测试对象，注入模拟的NameCollector
        $procedureList = new GetProcedureList($collector);

        // 执行方法并验证结果
        $result = $procedureList->execute();
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('testMethod', $result);
        $this->assertSame('TestClass', $result['testMethod']);
    }

    /**
     * 测试当NameCollector包含多个过程时，execute方法返回所有过程
     */
    public function testExecute_withMultipleProcedures(): void
    {
        // 要返回的多个过程数据
        $procedureData = [
            'method1' => 'Class1',
            'method2' => 'Class2',
            'method3' => 'Class3'
        ];

        // 创建NameCollector模拟对象
        $collector = $this->createMock(NameCollector::class);

        // 配置模拟对象的getProcedures方法返回多个过程
        $collector->method('getProcedures')
            ->willReturn($procedureData);

        // 创建被测试对象，注入模拟的NameCollector
        $procedureList = new GetProcedureList($collector);

        // 执行方法并验证结果
        $result = $procedureList->execute();
        $this->assertCount(count($procedureData), $result);

        foreach ($procedureData as $methodName => $className) {
            $this->assertArrayHasKey($methodName, $result);
            $this->assertSame($className, $result[$methodName]);
        }
    }

    /**
     * 测试类继承自BaseProcedure
     */
    public function testClass_extendsBaseProcedure(): void
    {
        $collector = $this->createMock(NameCollector::class);
        $procedureList = new GetProcedureList($collector);

        $this->assertInstanceOf(BaseProcedure::class, $procedureList);
    }

    /**
     * 测试类具有正确的MethodExpose属性
     */
    public function testClass_hasMethodExposeAttribute(): void
    {
        $reflection = new \ReflectionClass(GetProcedureList::class);
        $attributes = $reflection->getAttributes(MethodExpose::class);

        $this->assertCount(1, $attributes);

        $attribute = $attributes[0];
        $this->assertSame(MethodExpose::class, $attribute->getName());

        $arguments = $attribute->getArguments();
        // 检查是否为命名参数
        if (isset($arguments['method'])) {
            $this->assertSame('GetProcedureList', $arguments['method']);
        } else {
            $this->assertCount(1, $arguments);
            $this->assertSame('GetProcedureList', $arguments[0]);
        }
    }

    /**
     * 测试构造函数正确初始化
     */
    public function testConstruct_initializesCorrectly(): void
    {
        $collector = $this->createMock(NameCollector::class);
        $procedureList = new GetProcedureList($collector);

        // 使用反射验证collector属性被正确设置
        $reflection = new \ReflectionClass($procedureList);
        $collectorProperty = $reflection->getProperty('collector');
        $collectorProperty->setAccessible(true);

        $this->assertSame($collector, $collectorProperty->getValue($procedureList));
    }

    /**
     * 测试execute方法调用NameCollector的getProcedures方法
     */
    public function testExecute_callsCollectorGetProcedures(): void
    {
        $collector = $this->createMock(NameCollector::class);

        // 验证getProcedures方法被调用一次
        $collector->expects($this->once())
            ->method('getProcedures')
            ->willReturn([]);

        $procedureList = new GetProcedureList($collector);
        $procedureList->execute();
    }

    /**
     * 测试execute方法直接返回NameCollector的结果
     */
    public function testExecute_returnsCollectorResult(): void
    {
        $expectedResult = [
            'testMethod1' => 'TestClass1',
            'testMethod2' => 'TestClass2',
        ];

        $collector = $this->createMock(NameCollector::class);
        $collector->method('getProcedures')
            ->willReturn($expectedResult);

        $procedureList = new GetProcedureList($collector);
        $result = $procedureList->execute();

        // 验证返回的结果与NameCollector返回的结果完全相同
        $this->assertSame($expectedResult, $result);
    }
}
