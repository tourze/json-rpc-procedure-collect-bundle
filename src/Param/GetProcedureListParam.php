<?php

declare(strict_types=1);

namespace Tourze\JsonRPCProcedureCollectBundle\Param;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * GetProcedureList 参数类
 */
readonly class GetProcedureListParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '是否返回完整文档（包括参数信息）')]
        public bool $detailed = false,
    ) {
    }
}
