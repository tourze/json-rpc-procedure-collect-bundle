<?php

declare(strict_types=1);

namespace Tourze\JsonRPCProcedureCollectBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPCProcedureCollectBundle\JsonRPCProcedureCollectBundle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(JsonRPCProcedureCollectBundle::class)]
#[RunTestsInSeparateProcesses]
final class JsonRPCProcedureCollectBundleTest extends AbstractBundleTestCase
{
}
