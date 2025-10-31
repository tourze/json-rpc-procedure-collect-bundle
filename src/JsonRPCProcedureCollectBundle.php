<?php

declare(strict_types=1);

namespace Tourze\JsonRPCProcedureCollectBundle;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;

/**
 * JSON-RPC Procedure Collection Bundle
 *
 * 提供JSON-RPC过程收集和管理功能
 */
class JsonRPCProcedureCollectBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            FrameworkBundle::class => ['all' => true],
        ];
    }
}
