<?php

namespace Tourze\JsonRPCProcedureCollectBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;
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
} 