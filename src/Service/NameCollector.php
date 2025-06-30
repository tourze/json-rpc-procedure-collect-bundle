<?php

namespace Tourze\JsonRPCProcedureCollectBundle\Service;

use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Domain\JsonRpcMethodInterface;

/**
 * 记录所有可能的方法
 */
class NameCollector
{
    private array $procedures = [];

    /**
     * @param iterable<JsonRpcMethodInterface> $taggedMethods
     */
    public function __construct(iterable $taggedMethods = [])
    {
        $this->initializeProcedures($taggedMethods);
    }

    public function getProcedures(): array
    {
        return $this->procedures;
    }

    public function addProcedure(string $method, string $className): void
    {
        $this->procedures[$method] = $className;
    }

    private function initializeProcedures(iterable $taggedMethods): void
    {
        foreach ($taggedMethods as $method) {
            $reflection = new \ReflectionClass($method);
            $attributes = $reflection->getAttributes(MethodExpose::class);
            
            foreach ($attributes as $attribute) {
                $instance = $attribute->newInstance();
                // 从属性参数中获取方法名
                $args = $attribute->getArguments();
                
                // 处理命名参数和位置参数两种情况
                $methodName = null;
                if (isset($args['method'])) {
                    // 命名参数
                    $methodName = $args['method'];
                } elseif (!empty($args[0])) {
                    // 位置参数
                    $methodName = $args[0];
                }
                
                if ($methodName) {
                    $this->addProcedure($methodName, $reflection->getName());
                }
            }
        }
    }
}
