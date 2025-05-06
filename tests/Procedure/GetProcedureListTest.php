<?php

namespace Tourze\JsonRPCProcedureCollectBundle\Tests\Procedure;

use PHPUnit\Framework\TestCase;
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

        $this->assertIsArray($result);
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

        $this->assertIsArray($result);
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

        $this->assertIsArray($result);
        $this->assertCount(count($procedureData), $result);

        foreach ($procedureData as $methodName => $className) {
            $this->assertArrayHasKey($methodName, $result);
            $this->assertSame($className, $result[$methodName]);
        }
    }
}
