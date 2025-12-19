<?php

declare(strict_types=1);

namespace Tourze\JsonRPCProcedureCollectBundle\Tests\Param;

use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * TestProcedure 参数类
 *
 * 该方法无参数,提供空参数对象以满足框架要求
 */
readonly class TestProcedureParam implements RpcParamInterface
{
    public function __construct()
    {
    }
}
