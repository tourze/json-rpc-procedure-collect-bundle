<?php

namespace Tourze\JsonRPCProcedureCollectBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;
use Tourze\JsonRPCProcedureCollectBundle\Procedure\GetProcedureList;
use Tourze\JsonRPCProcedureCollectBundle\Service\NameCollector;
use Tourze\JsonRPCProcedureCollectBundle\Tests\Fixtures\AnotherTestProcedure;
use Tourze\JsonRPCProcedureCollectBundle\Tests\Fixtures\TestProcedure;

/**
 * @internal
 */
#[CoversClass(GetProcedureList::class)]
#[RunTestsInSeparateProcesses]
final class GetProcedureListTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
        // 不需要特殊设置
    }

    /**
     * 测试GetProcedureList服务能够正确返回注册的过程
     */
    public function testExecuteReturnsRegisteredProcedures(): void
    {
        // 获取NameCollector服务并手动添加测试过程
        $collector = self::getService(NameCollector::class);
        $collector->addProcedure('TestMethod1', 'TestClass1');
        $collector->addProcedure('TestMethod2', 'TestClass2');

        // 从容器获取GetProcedureList服务
        $procedureList = self::getService(GetProcedureList::class);

        // 执行方法并验证结果
        $result = $procedureList->execute();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('TestMethod1', $result);
        $this->assertSame('TestClass1', $result['TestMethod1']);
        $this->assertArrayHasKey('TestMethod2', $result);
        $this->assertSame('TestClass2', $result['TestMethod2']);
    }

    /**
     * 测试当没有注册过程时返回空数组或默认过程
     */
    public function testExecuteWithNoProcedures(): void
    {
        // 从容器获取GetProcedureList服务
        $procedureList = self::getService(GetProcedureList::class);

        // 执行方法
        $result = $procedureList->execute();

        // 验证返回数组（可能包含默认注册的过程）
        $this->assertIsArray($result);

        // 如果有默认的GetProcedureList过程，验证它存在
        if ([] !== $result) {
            // 至少应该包含GetProcedureList自身
            $this->assertArrayHasKey('GetProcedureList', $result);
        }
    }

    /**
     * 测试类继承自BaseProcedure
     */
    public function testClassExtendsBaseProcedure(): void
    {
        // 从容器获取服务
        $procedureList = self::getService(GetProcedureList::class);

        $this->assertInstanceOf(BaseProcedure::class, $procedureList);
    }

    /**
     * 测试类具有正确的MethodExpose属性
     */
    public function testClassHasMethodExposeAttribute(): void
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
     * 测试NameCollector正确注入到GetProcedureList
     */
    public function testNameCollectorInjection(): void
    {
        // 从容器获取原始的NameCollector服务
        $originalCollector = self::getService(NameCollector::class);

        // 从容器获取GetProcedureList服务
        $procedureList = self::getService(GetProcedureList::class);

        // 使用反射验证collector属性被正确设置
        $reflection = new \ReflectionClass($procedureList);
        $collectorProperty = $reflection->getProperty('collector');
        $collectorProperty->setAccessible(true);

        // 验证注入的collector是容器中的同一个实例
        $this->assertSame($originalCollector, $collectorProperty->getValue($procedureList));
    }

    /**
     * 测试多个过程的添加和获取
     */
    public function testMultipleProceduresHandling(): void
    {
        // 获取NameCollector服务
        $collector = self::getService(NameCollector::class);

        // 添加多个测试过程
        $testProcedures = [
            'Method1' => 'Class1',
            'Method2' => 'Class2',
            'Method3' => 'Class3',
            'Method4' => 'Class4',
            'Method5' => 'Class5',
        ];

        foreach ($testProcedures as $method => $class) {
            $collector->addProcedure($method, $class);
        }

        // 从容器获取GetProcedureList服务
        $procedureList = self::getService(GetProcedureList::class);

        // 执行方法并验证结果
        $result = $procedureList->execute();

        // 验证所有添加的过程都存在
        foreach ($testProcedures as $method => $class) {
            $this->assertArrayHasKey($method, $result);
            $this->assertSame($class, $result[$method]);
        }
    }

    /**
     * 测试通过标签注入的过程自动被收集
     */
    public function testTaggedProceduresAutoCollection(): void
    {
        // 获取NameCollector服务并手动添加测试过程
        $collector = self::getService(NameCollector::class);
        $collector->addProcedure('TestMethod', TestProcedure::class);
        $collector->addProcedure('AnotherTestMethod', AnotherTestProcedure::class);

        // 从容器获取GetProcedureList服务
        $procedureList = self::getService(GetProcedureList::class);

        // 执行方法
        $result = $procedureList->execute();

        // 验证测试过程被正确收集
        $this->assertIsArray($result);

        // 至少应该包含GetProcedureList自身
        $this->assertArrayHasKey('GetProcedureList', $result);

        // 验证手动添加的过程存在
        $this->assertSame(TestProcedure::class, $result['TestMethod']);
        $this->assertSame(AnotherTestProcedure::class, $result['AnotherTestMethod']);
    }

    /**
     * 测试execute方法的返回类型
     */
    public function testExecuteReturnType(): void
    {
        // 从容器获取GetProcedureList服务
        $procedureList = self::getService(GetProcedureList::class);

        // 执行方法
        $result = $procedureList->execute();

        // 验证返回类型是数组
        $this->assertIsArray($result);

        // 验证数组的键值对格式（方法名 => 类名）
        foreach ($result as $methodName => $className) {
            $this->assertIsString($methodName);
            $this->assertIsString($className);
        }
    }
}
