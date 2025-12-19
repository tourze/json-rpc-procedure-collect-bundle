<?php

declare(strict_types=1);

namespace Tourze\JsonRPCProcedureCollectBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

final class JsonRPCProcedureCollectExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return __DIR__ . '/../Resources/config';
    }
}
