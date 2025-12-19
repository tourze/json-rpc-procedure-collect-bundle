<?php

declare(strict_types=1);

namespace Tourze\JsonRPCProcedureCollectBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPCProcedureCollectBundle\Param\GetProcedureListParam;

/**
 * @internal
 */
#[CoversClass(GetProcedureListParam::class)]
class GetProcedureListParamTest extends TestCase
{
    public function testImplementsRpcParamInterface(): void
    {
        $param = new GetProcedureListParam();
        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testCanBeInstantiated(): void
    {
        $param = new GetProcedureListParam();
        $this->assertInstanceOf(GetProcedureListParam::class, $param);
    }
}
