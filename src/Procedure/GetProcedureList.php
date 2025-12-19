<?php

declare(strict_types=1);

namespace Tourze\JsonRPCProcedureCollectBundle\Procedure;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPCProcedureCollectBundle\Param\GetProcedureListParam;
use Tourze\JsonRPCProcedureCollectBundle\Service\NameCollectorInterface;
use Tourze\JsonRPCProcedureCollectBundle\Service\ProcedureDocCollector;

#[MethodTag(name: '系统服务')]
#[MethodDoc(summary: '获取过程列表', description: '返回所有可用的 JsonRPC 方法列表，可选返回完整文档')]
#[MethodExpose(method: 'GetProcedureList')]
#[Autoconfigure(public: true)]
class GetProcedureList extends BaseProcedure
{
    public function __construct(
        private readonly NameCollectorInterface $collector,
        private readonly ProcedureDocCollector $docCollector,
    ) {
    }

    /**
     * @phpstan-param GetProcedureListParam $param
     */
    public function execute(GetProcedureListParam|RpcParamInterface $param): ArrayResult
    {
        if ($param->detailed) {
            return new ArrayResult($this->docCollector->getFullDocumentation());
        }

        return new ArrayResult($this->collector->getProcedures());
    }
}
