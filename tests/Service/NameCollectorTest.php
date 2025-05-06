<?php

namespace Tourze\JsonRPCProcedureCollectBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Tourze\JsonRPCProcedureCollectBundle\Service\NameCollector;

class NameCollectorTest extends TestCase
{
    /**
     * 测试初始状态下getProcedures返回空数组
     */
    public function testGetProcedures_initiallyEmpty(): void
    {
        $collector = new NameCollector();
        $this->assertSame([], $collector->getProcedures());
    }

    /**
     * 测试添加单个过程后能正确获取
     */
    public function testAddProcedure_singleMethod(): void
    {
        $collector = new NameCollector();

        $methodName = 'testMethod';
        $className = 'TestClass';

        $collector->addProcedure($methodName, $className);

        $procedures = $collector->getProcedures();

        $this->assertCount(1, $procedures);
        $this->assertArrayHasKey($methodName, $procedures);
        $this->assertSame($className, $procedures[$methodName]);
    }

    /**
     * 测试添加多个过程后能正确获取所有
     */
    public function testAddProcedure_multipleMethods(): void
    {
        $collector = new NameCollector();

        $methods = [
            'method1' => 'Class1',
            'method2' => 'Class2',
            'method3' => 'Class3',
        ];

        foreach ($methods as $methodName => $className) {
            $collector->addProcedure($methodName, $className);
        }

        $procedures = $collector->getProcedures();

        $this->assertCount(count($methods), $procedures);

        foreach ($methods as $methodName => $className) {
            $this->assertArrayHasKey($methodName, $procedures);
            $this->assertSame($className, $procedures[$methodName]);
        }
    }

    /**
     * 测试同名方法会被覆盖
     */
    public function testAddProcedure_overwritesExistingMethod(): void
    {
        $collector = new NameCollector();

        $methodName = 'duplicateMethod';
        $className1 = 'OriginalClass';
        $className2 = 'ReplacementClass';

        $collector->addProcedure($methodName, $className1);
        $this->assertSame($className1, $collector->getProcedures()[$methodName]);

        $collector->addProcedure($methodName, $className2);

        $procedures = $collector->getProcedures();

        $this->assertCount(1, $procedures);
        $this->assertArrayHasKey($methodName, $procedures);
        $this->assertSame($className2, $procedures[$methodName]);
    }
}
