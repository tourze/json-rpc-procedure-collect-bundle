<?php

declare(strict_types=1);

namespace Tourze\JsonRPCProcedureCollectBundle\Service;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Domain\JsonRpcMethodInterface;
use Tourze\JsonRPCProcedureCollectBundle\Procedure\GetProcedureList;

/**
 * 记录所有可能的方法
 */
#[Autoconfigure(public: true)]
class NameCollector implements NameCollectorInterface
{
    /**
     * @var array<string, string>
     */
    private array $procedures = [];

    private bool $initialized = false;

    /**
     * @param iterable<JsonRpcMethodInterface> $taggedMethods
     */
    public function __construct(
        #[AutowireIterator(tag: 'json_rpc_http_server.jsonrpc_method')]
        private readonly iterable $taggedMethods = [],
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function getProcedures(): array
    {
        if (!$this->initialized) {
            $this->initializeProcedures();
        }

        return $this->procedures;
    }

    public function addProcedure(string $method, string $className): void
    {
        $this->procedures[$method] = $className;
    }

    private function initializeProcedures(): void
    {
        foreach ($this->taggedMethods as $method) {
            // 跳过 GetProcedureList 本身以避免循环依赖
            if ($method instanceof GetProcedureList) {
                continue;
            }

            $this->processMethodAttributes($method);
        }

        // 延迟加载 GetProcedureList
        $this->addGetProcedureListLazily();

        $this->initialized = true;
    }

    private function processMethodAttributes(JsonRpcMethodInterface $method): void
    {
        $reflection = new \ReflectionClass($method);
        $attributes = $reflection->getAttributes(MethodExpose::class);

        foreach ($attributes as $attribute) {
            $args = $attribute->getArguments();

            // 处理命名参数和位置参数两种情况
            $methodName = null;
            if (isset($args['method'])) {
                // 命名参数
                $methodName = $args['method'];
            } elseif (isset($args[0]) && '' !== $args[0]) {
                // 位置参数
                $methodName = $args[0];
            }

            if (null !== $methodName && is_string($methodName)) {
                $this->addProcedure($methodName, $reflection->getName());
            }
        }
    }

    private function addGetProcedureListLazily(): void
    {
        // 直接通过反射获取 GetProcedureList 的属性信息，不实例化对象
        $reflection = new \ReflectionClass(GetProcedureList::class);
        $attributes = $reflection->getAttributes(MethodExpose::class);

        foreach ($attributes as $attribute) {
            $args = $attribute->getArguments();

            $methodName = null;
            if (isset($args['method'])) {
                $methodName = $args['method'];
            } elseif (isset($args[0]) && '' !== $args[0]) {
                $methodName = $args[0];
            }

            if (null !== $methodName && is_string($methodName)) {
                $this->addProcedure($methodName, $reflection->getName());
            }
        }
    }
}
