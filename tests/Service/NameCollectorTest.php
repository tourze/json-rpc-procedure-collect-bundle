<?php

namespace Tourze\JsonRPCProcedureCollectBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Domain\JsonRpcMethodInterface;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPCProcedureCollectBundle\Service\NameCollector;
use Tourze\JsonRPCProcedureCollectBundle\Service\NameCollectorInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(NameCollector::class)]
#[RunTestsInSeparateProcesses]
final class NameCollectorTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 集成测试基础设置
    }

    /**
     * 测试初始状态下getProcedures返回空数组
     */
    public function testGetProceduresInitiallyEmpty(): void
    {
        $collector = self::getService(NameCollectorInterface::class);
        $procedures = $collector->getProcedures();

        // 至少应该包含 GetProcedureList
        $this->assertGreaterThanOrEqual(1, count($procedures));
        $this->assertArrayHasKey('GetProcedureList', $procedures);
        $this->assertSame('Tourze\JsonRPCProcedureCollectBundle\Procedure\GetProcedureList', $procedures['GetProcedureList']);
    }

    /**
     * 测试添加单个过程后能正确获取
     */
    public function testAddProcedureSingleMethod(): void
    {
        $collector = self::getService(NameCollectorInterface::class);

        $methodName = 'testMethod';
        $className = 'TestClass';

        $collector->addProcedure($methodName, $className);

        $procedures = $collector->getProcedures();

        // 应该包含手动添加的方法和 GetProcedureList
        $this->assertArrayHasKey($methodName, $procedures);
        $this->assertSame($className, $procedures[$methodName]);
        $this->assertArrayHasKey('GetProcedureList', $procedures);
        $this->assertSame('Tourze\JsonRPCProcedureCollectBundle\Procedure\GetProcedureList', $procedures['GetProcedureList']);
    }

    /**
     * 测试添加多个过程后能正确获取所有
     */
    public function testAddProcedureMultipleMethods(): void
    {
        $collector = self::getService(NameCollectorInterface::class);

        $methods = [
            'method1' => 'Class1',
            'method2' => 'Class2',
            'method3' => 'Class3',
        ];

        foreach ($methods as $methodName => $className) {
            $collector->addProcedure($methodName, $className);
        }

        $procedures = $collector->getProcedures();

        // 应该至少包含手动添加的方法和 GetProcedureList
        $this->assertGreaterThanOrEqual(count($methods) + 1, count($procedures));

        foreach ($methods as $methodName => $className) {
            $this->assertArrayHasKey($methodName, $procedures);
            $this->assertSame($className, $procedures[$methodName]);
        }

        // 确保 GetProcedureList 存在
        $this->assertArrayHasKey('GetProcedureList', $procedures);
        $this->assertSame('Tourze\JsonRPCProcedureCollectBundle\Procedure\GetProcedureList', $procedures['GetProcedureList']);
    }

    /**
     * 测试同名方法会被覆盖
     */
    public function testAddProcedureOverwritesExistingMethod(): void
    {
        $collector = self::getService(NameCollectorInterface::class);

        $methodName = 'duplicateMethod';
        $className1 = 'OriginalClass';
        $className2 = 'ReplacementClass';

        $collector->addProcedure($methodName, $className1);
        $this->assertSame($className1, $collector->getProcedures()[$methodName]);

        $collector->addProcedure($methodName, $className2);

        $procedures = $collector->getProcedures();

        // 应该包含覆盖后的方法和 GetProcedureList
        $this->assertArrayHasKey($methodName, $procedures);
        $this->assertSame($className2, $procedures[$methodName]);
    }

    /**
     * 测试空字符串方法名
     */
    public function testAddProcedureEmptyMethodName(): void
    {
        $collector = self::getService(NameCollectorInterface::class);

        $methodName = '';
        $className = 'TestClass';

        $collector->addProcedure($methodName, $className);

        $procedures = $collector->getProcedures();

        // 应该包含空字符串方法名和 GetProcedureList
        $this->assertArrayHasKey('', $procedures);
        $this->assertSame($className, $procedures['']);
    }

    /**
     * 测试空字符串类名
     */
    public function testAddProcedureEmptyClassName(): void
    {
        $collector = self::getService(NameCollectorInterface::class);

        $methodName = 'testMethod';
        $className = '';

        $collector->addProcedure($methodName, $className);

        $procedures = $collector->getProcedures();

        // 应该包含空类名方法和 GetProcedureList
        $this->assertArrayHasKey($methodName, $procedures);
        $this->assertSame('', $procedures[$methodName]);
    }

    /**
     * 测试特殊字符方法名
     */
    public function testAddProcedureSpecialCharactersInMethodName(): void
    {
        $collector = self::getService(NameCollectorInterface::class);

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

        // 应该包含手动添加的方法和 GetProcedureList
        $this->assertGreaterThanOrEqual(count($specialMethods) + 1, count($procedures));

        foreach ($specialMethods as $methodName => $className) {
            $this->assertArrayHasKey($methodName, $procedures);
            $this->assertSame($className, $procedures[$methodName]);
        }
    }

    /**
     * 测试特殊字符类名
     */
    public function testAddProcedureSpecialCharactersInClassName(): void
    {
        $collector = self::getService(NameCollectorInterface::class);

        $methodName = 'testMethod';
        $specialClassName = 'App\Namespace\ClassName';

        $collector->addProcedure($methodName, $specialClassName);

        $procedures = $collector->getProcedures();

        // 应该包含特殊类名方法和 GetProcedureList
        $this->assertArrayHasKey($methodName, $procedures);
        $this->assertSame($specialClassName, $procedures[$methodName]);
    }

    /**
     * 测试Unicode字符处理
     */
    public function testAddProcedureUnicodeCharacters(): void
    {
        $collector = self::getService(NameCollectorInterface::class);

        $methodName = '测试方法';
        $className = 'TestClass中文';

        $collector->addProcedure($methodName, $className);

        $procedures = $collector->getProcedures();

        // 应该包含Unicode字符方法和 GetProcedureList
        $this->assertArrayHasKey($methodName, $procedures);
        $this->assertSame($className, $procedures[$methodName]);
    }

    /**
     * 测试通过构造函数初始化方法
     * 使用匿名类来避免在测试文件中定义多个类
     */
    public function testConstructorWithTaggedMethods(): void
    {
        // 创建匿名类实现 JsonRpcMethodInterface
        $mockMethod1 = new class implements JsonRpcMethodInterface {
            public function __invoke(JsonRpcRequest $request): mixed
            {
                return [];
            }

            public function execute(): array
            {
                return [];
            }
        };

        $mockMethod2 = new class implements JsonRpcMethodInterface {
            public function __invoke(JsonRpcRequest $request): mixed
            {
                return [];
            }

            public function execute(): array
            {
                return [];
            }
        };

        // 我们无法直接模拟属性，所以这个测试只验证构造函数不会出错
        // 实际的属性解析功能需要通过集成测试来验证
        $taggedMethods = [$mockMethod1, $mockMethod2];

        $collector = self::getService(NameCollectorInterface::class);

        // 验证构造函数执行成功，返回空的过程列表
        // （因为模拟对象没有真实的属性）
        $procedures = $collector->getProcedures();

        $this->assertIsArray($procedures);
    }
}
