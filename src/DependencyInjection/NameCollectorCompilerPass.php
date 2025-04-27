<?php

namespace Tourze\JsonRPCProcedureCollectBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Domain\JsonRpcMethodInterface;
use Tourze\JsonRPCProcedureCollectBundle\Service\NameCollector;

class NameCollectorCompilerPass implements CompilerPassInterface
{
    final public const JSONRPC_METHOD_TAG_METHOD_NAME_KEY = 'method';

    public function process(ContainerBuilder $container): void
    {
        $nameCollector = $container->getDefinition(NameCollector::class);

        // 方法定义
        foreach ($container->findTaggedServiceIds(MethodExpose::JSONRPC_METHOD_TAG) as $serviceId => $tagAttributeList) {
            $procedureDef = $container->getDefinition($serviceId);
            static::validateJsonRpcMethodDefinition($serviceId, $procedureDef);
            foreach ($tagAttributeList as $tagAttributeKey => $tagAttributeData) {
                static::validateJsonRpcMethodTagAttributes($serviceId, $tagAttributeData);
                $methodName = $tagAttributeData[self::JSONRPC_METHOD_TAG_METHOD_NAME_KEY];
                $nameCollector->addMethodCall('addProcedure', [$methodName, $procedureDef->getClass()]);
            }
        }
    }

    private static function validateJsonRpcMethodTagAttributes(string $serviceId, array $tagAttributeData): void
    {
        if (!isset($tagAttributeData[self::JSONRPC_METHOD_TAG_METHOD_NAME_KEY])) {
            throw new LogicException(sprintf('Service "%s" is taggued as JSON-RPC method but does not have method name defined under "%s" tag attribute key', $serviceId, self::JSONRPC_METHOD_TAG_METHOD_NAME_KEY));
        }
    }

    /**
     * @throws \LogicException In case definition is not valid
     */
    private static function validateJsonRpcMethodDefinition(string $serviceId, Definition $definition): void
    {
        if (!in_array(JsonRpcMethodInterface::class, class_implements($definition->getClass()))) {
            throw new LogicException(sprintf('Service "%s" is taggued as JSON-RPC method but does not implement %s', $serviceId, JsonRpcMethodInterface::class));
        }
    }
}
