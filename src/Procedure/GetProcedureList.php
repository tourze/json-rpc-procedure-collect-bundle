<?php

declare(strict_types=1);

namespace Tourze\JsonRPCProcedureCollectBundle\Procedure;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPCProcedureCollectBundle\Service\NameCollectorInterface;

#[MethodTag(name: '系统服务')]
#[MethodDoc(summary: '获取过程列表')]
#[MethodExpose(method: 'GetProcedureList')]
#[Autoconfigure(public: true)]
class GetProcedureList extends BaseProcedure
{
    public function __construct(private readonly NameCollectorInterface $collector)
    {
    }

    public function execute(): array
    {
        return $this->collector->getProcedures();
    }
}
