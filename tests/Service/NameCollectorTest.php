<?php

namespace Tourze\JsonRPCProcedureCollectBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Domain\JsonRpcMethodInterface;
use Tourze\JsonRPCProcedureCollectBundle\Service\NameCollector;

#[MethodExpose(method: 'TestMethod1')]
class TestJsonRpcMethod1 implements JsonRpcMethodInterface
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

#[MethodExpose(method: 'TestMethod2')]
#[MethodExpose(method: 'TestMethod3')]
class TestJsonRpcMethod2 implements JsonRpcMethodInterface
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

class NameCollectorTest extends TestCase
{
    /**
     * 测试初始状态下getProcedures返回空数组
     */
    public function testGetProcedures_initiallyEmpty(): void
    {
        $collector = new NameCollector([]);
        $this->assertSame([], $collector->getProcedures());
    }

    /**
     * 测试添加单个过程后能正确获取
     */
    public function testAddProcedure_singleMethod(): void
    {
        $collector = new NameCollector([]);

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
        $collector = new NameCollector([]);

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
        $collector = new NameCollector([]);

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

    /**
     * 测试空字符串方法名
     */
    public function testAddProcedure_emptyMethodName(): void
    {
        $collector = new NameCollector([]);
        
        $methodName = '';
        $className = 'TestClass';
        
        $collector->addProcedure($methodName, $className);
        
        $procedures = $collector->getProcedures();
        
        $this->assertCount(1, $procedures);
        $this->assertArrayHasKey('', $procedures);
        $this->assertSame($className, $procedures['']);
    }

    /**
     * 测试空字符串类名
     */
    public function testAddProcedure_emptyClassName(): void
    {
        $collector = new NameCollector([]);
        
        $methodName = 'testMethod';
        $className = '';
        
        $collector->addProcedure($methodName, $className);
        
        $procedures = $collector->getProcedures();
        
        $this->assertCount(1, $procedures);
        $this->assertArrayHasKey($methodName, $procedures);
        $this->assertSame('', $procedures[$methodName]);
    }

    /**
     * 测试特殊字符方法名
     */
    public function testAddProcedure_specialCharactersInMethodName(): void
    {
        $collector = new NameCollector([]);
        
        $specialMethods = [
            'method.with.dots' => 'TestClass1',
            'method-with-dashes' => 'TestClass2',
            'method_with_underscores' => 'TestClass3',
            'method123WithNumbers' => 'TestClass4',
            'UPPERCASE_METHOD' => 'TestClass5',
            'MethodWithCamelCase' => 'TestClass6',
        ];
        
        foreach ($specialMethods as $methodName => $className) {
            $collector->addProcedure($methodName, $className);
        }
        
        $procedures = $collector->getProcedures();
        
        $this->assertCount(count($specialMethods), $procedures);
        
        foreach ($specialMethods as $methodName => $className) {
            $this->assertArrayHasKey($methodName, $procedures);
            $this->assertSame($className, $procedures[$methodName]);
        }
    }

    /**
     * 测试特殊字符类名
     */
    public function testAddProcedure_specialCharactersInClassName(): void
    {
        $collector = new NameCollector([]);
        
        $methodName = 'testMethod';
        $specialClassName = 'App\\Namespace\\ClassName';
        
        $collector->addProcedure($methodName, $specialClassName);
        
        $procedures = $collector->getProcedures();
        
        $this->assertCount(1, $procedures);
        $this->assertArrayHasKey($methodName, $procedures);
        $this->assertSame($specialClassName, $procedures[$methodName]);
    }

    /**
     * 测试Unicode字符处理
     */
    public function testAddProcedure_unicodeCharacters(): void
    {
        $collector = new NameCollector([]);
        
        $methodName = '测试方法';
        $className = 'TestClass中文';
        
        $collector->addProcedure($methodName, $className);
        
        $procedures = $collector->getProcedures();
        
        $this->assertCount(1, $procedures);
        $this->assertArrayHasKey($methodName, $procedures);
        $this->assertSame($className, $procedures[$methodName]);
    }

    /**
     * 测试通过构造函数初始化方法
     */
    public function testConstructor_withTaggedMethods(): void
    {
        $method1 = new TestJsonRpcMethod1();
        $method2 = new TestJsonRpcMethod2();
        
        $taggedMethods = [$method1, $method2];
        
        $collector = new NameCollector($taggedMethods);
        
        $procedures = $collector->getProcedures();
        
        // TestJsonRpcMethod1 有1个方法，TestJsonRpcMethod2 有2个方法
        $this->assertCount(3, $procedures);
        
        $this->assertArrayHasKey('TestMethod1', $procedures);
        $this->assertSame(TestJsonRpcMethod1::class, $procedures['TestMethod1']);
        
        $this->assertArrayHasKey('TestMethod2', $procedures);
        $this->assertSame(TestJsonRpcMethod2::class, $procedures['TestMethod2']);
        
        $this->assertArrayHasKey('TestMethod3', $procedures);
        $this->assertSame(TestJsonRpcMethod2::class, $procedures['TestMethod3']);
    }
}
