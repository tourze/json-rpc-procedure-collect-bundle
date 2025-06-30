<?php

namespace Tourze\JsonRPCProcedureCollectBundle\Procedure;

use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPCProcedureCollectBundle\Service\NameCollector;

#[MethodExpose(method: 'GetProcedureList')]
class GetProcedureList extends BaseProcedure
{
    public function __construct(private readonly NameCollector $collector)
    {
    }

    public function execute(): array
    {
        return $this->collector->getProcedures();
    }
}
