<?php

namespace Tourze\JsonRPCProcedureCollectBundle\Tests\Fixtures;

use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

#[MethodExpose(method: 'TestMethod')]
class TestProcedure extends BaseProcedure
{
    public function execute(): array
    {
        return ['result' => 'test'];
    }
}
