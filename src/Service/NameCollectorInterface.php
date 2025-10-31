<?php

declare(strict_types=1);

namespace Tourze\JsonRPCProcedureCollectBundle\Service;

interface NameCollectorInterface
{
    /**
     * @return array<string, string>
     */
    public function getProcedures(): array;

    public function addProcedure(string $method, string $className): void;
}
