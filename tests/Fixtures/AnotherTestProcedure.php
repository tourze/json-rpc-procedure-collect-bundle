<?php

namespace Tourze\JsonRPCProcedureCollectBundle\Tests\Fixtures;

use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

#[MethodExpose(method: 'AnotherTestMethod')]
class AnotherTestProcedure extends BaseProcedure
{
    public function execute(): array
    {
        return ['result' => 'another test'];
    }
}
