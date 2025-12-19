<?php

namespace Tourze\JsonRPCProcedureCollectBundle\Tests\Fixtures;

use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPCProcedureCollectBundle\Tests\Param\AnotherTestProcedureParam;

#[MethodExpose(method: 'AnotherTestMethod')]
class AnotherTestProcedure extends BaseProcedure
{
    public function execute(AnotherTestProcedureParam|RpcParamInterface $param): array
    {
        return ['result' => 'another test'];
    }
}
