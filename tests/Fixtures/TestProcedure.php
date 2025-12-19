<?php

namespace Tourze\JsonRPCProcedureCollectBundle\Tests\Fixtures;

use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPCProcedureCollectBundle\Tests\Param\TestProcedureParam;

#[MethodExpose(method: 'TestMethod')]
class TestProcedure extends BaseProcedure
{
    public function execute(TestProcedureParam|RpcParamInterface $param): array
    {
        return ['result' => 'test'];
    }
}
