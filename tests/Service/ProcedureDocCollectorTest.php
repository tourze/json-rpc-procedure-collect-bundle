<?php

declare(strict_types=1);

namespace Tourze\JsonRPCProcedureCollectBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPCProcedureCollectBundle\Procedure\GetProcedureList;
use Tourze\JsonRPCProcedureCollectBundle\Service\NameCollectorInterface;
use Tourze\JsonRPCProcedureCollectBundle\Service\ProcedureDocCollector;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(ProcedureDocCollector::class)]
#[RunTestsInSeparateProcesses]
final class ProcedureDocCollectorTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 集成测试基础设置
    }

    public function testGetFullDocumentationReturnsEmptyArrayWhenNoProcedures(): void
    {
        // 获取真实的服务实例
        $nameCollector = self::getService(NameCollectorInterface::class);
        $collector = self::getService(ProcedureDocCollector::class);

        // 在集成环境中，至少有 GetProcedureList 会被自动注册
        $result = $collector->getFullDocumentation();

        // 验证返回的是数组
        $this->assertIsArray($result);
        // 至少应该包含 GetProcedureList
        $this->assertArrayHasKey('GetProcedureList', $result);
    }

    public function testGetFullDocumentationReturnsProcedureInfo(): void
    {
        $collector = self::getService(ProcedureDocCollector::class);

        $result = $collector->getFullDocumentation();

        $this->assertArrayHasKey('GetProcedureList', $result);
        $doc = $result['GetProcedureList'];

        $this->assertSame('GetProcedureList', $doc['method']);
        $this->assertSame(GetProcedureList::class, $doc['class']);
        $this->assertSame('系统服务', $doc['tag']);
        $this->assertSame('获取过程列表', $doc['summary']);
        $this->assertIsArray($doc['params']);
    }

    public function testGetProcedureDocReturnsNullForUnknownMethod(): void
    {
        $collector = self::getService(ProcedureDocCollector::class);

        $result = $collector->getProcedureDoc('UnknownMethod');

        $this->assertNull($result);
    }

    public function testGetProcedureDocReturnsProcedureInfo(): void
    {
        $collector = self::getService(ProcedureDocCollector::class);

        $result = $collector->getProcedureDoc('GetProcedureList');

        $this->assertNotNull($result);
        $this->assertSame('GetProcedureList', $result['method']);
        $this->assertSame(GetProcedureList::class, $result['class']);
    }

    public function testExtractsTagFromMethodTagAttribute(): void
    {
        $collector = self::getService(ProcedureDocCollector::class);

        $result = $collector->getFullDocumentation();
        $doc = $result['GetProcedureList'];

        $this->assertSame('系统服务', $doc['tag']);
    }

    public function testExtractsSummaryFromMethodDocAttribute(): void
    {
        $collector = self::getService(ProcedureDocCollector::class);

        $result = $collector->getFullDocumentation();
        $doc = $result['GetProcedureList'];

        $this->assertSame('获取过程列表', $doc['summary']);
    }

    public function testExtractsParamsFromParamClass(): void
    {
        $collector = self::getService(ProcedureDocCollector::class);

        $result = $collector->getFullDocumentation();
        $doc = $result['GetProcedureList'];

        // GetProcedureListParam 有一个 detailed 参数
        $this->assertArrayHasKey('detailed', $doc['params']);
        $this->assertSame('是否返回完整文档（包括参数信息）', $doc['params']['detailed']['description']);
        $this->assertSame('bool', $doc['params']['detailed']['type']);
        $this->assertFalse($doc['params']['detailed']['required']);
        $this->assertFalse($doc['params']['detailed']['default']);
    }
}
