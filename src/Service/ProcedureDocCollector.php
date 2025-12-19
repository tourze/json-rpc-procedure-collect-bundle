<?php

declare(strict_types=1);

namespace Tourze\JsonRPCProcedureCollectBundle\Service;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Helper\ParamDocExtractor;

/**
 * 收集 Procedure 的完整文档信息
 *
 * 包括方法名、类名、标签、描述、参数文档等
 */
#[Autoconfigure(public: true)]
class ProcedureDocCollector
{
    public function __construct(
        private readonly NameCollectorInterface $nameCollector,
    ) {
    }

    /**
     * 获取所有 Procedure 的完整文档
     *
     * @return array<string, array{
     *     method: string,
     *     class: string,
     *     tag: string|null,
     *     summary: string|null,
     *     description: string|null,
     *     params: array<string, array{
     *         name: string,
     *         type: string,
     *         description: string,
     *         required: bool,
     *         default: mixed,
     *         constraints: array<string>
     *     }>
     * }>
     */
    public function getFullDocumentation(): array
    {
        $procedures = $this->nameCollector->getProcedures();
        $result = [];

        foreach ($procedures as $method => $className) {
            $result[$method] = $this->extractProcedureDoc($method, $className);
        }

        return $result;
    }

    /**
     * 获取单个 Procedure 的文档
     *
     * @return array{
     *     method: string,
     *     class: string,
     *     tag: string|null,
     *     summary: string|null,
     *     description: string|null,
     *     params: array<string, array{
     *         name: string,
     *         type: string,
     *         description: string,
     *         required: bool,
     *         default: mixed,
     *         constraints: array<string>
     *     }>
     * }|null
     */
    public function getProcedureDoc(string $method): ?array
    {
        $procedures = $this->nameCollector->getProcedures();

        if (!isset($procedures[$method])) {
            return null;
        }

        return $this->extractProcedureDoc($method, $procedures[$method]);
    }

    /**
     * 提取 Procedure 的文档信息
     *
     * @return array{
     *     method: string,
     *     class: string,
     *     tag: string|null,
     *     summary: string|null,
     *     description: string|null,
     *     params: array<string, array{
     *         name: string,
     *         type: string,
     *         description: string,
     *         required: bool,
     *         default: mixed,
     *         constraints: array<string>
     *     }>
     * }
     */
    private function extractProcedureDoc(string $method, string $className): array
    {
        $reflection = new \ReflectionClass($className);

        return [
            'method' => $method,
            'class' => $className,
            'tag' => $this->extractTag($reflection),
            'summary' => $this->extractSummary($reflection),
            'description' => $this->extractDescription($reflection),
            'params' => $this->extractParams($reflection),
        ];
    }

    private function extractTag(\ReflectionClass $reflection): ?string
    {
        $attributes = $reflection->getAttributes(MethodTag::class);

        if ([] === $attributes) {
            return null;
        }

        $tag = $attributes[0]->newInstance();

        return $tag->name;
    }

    private function extractSummary(\ReflectionClass $reflection): ?string
    {
        $attributes = $reflection->getAttributes(MethodDoc::class);

        if ([] === $attributes) {
            return null;
        }

        $doc = $attributes[0]->newInstance();

        return $doc->summary;
    }

    private function extractDescription(\ReflectionClass $reflection): ?string
    {
        $attributes = $reflection->getAttributes(MethodDoc::class);

        if ([] === $attributes) {
            return null;
        }

        $doc = $attributes[0]->newInstance();

        return $doc->description;
    }

    /**
     * 提取参数文档
     *
     * @return array<string, array{
     *     name: string,
     *     type: string,
     *     description: string,
     *     required: bool,
     *     default: mixed,
     *     constraints: array<string>
     * }>
     */
    private function extractParams(\ReflectionClass $reflection): array
    {
        $paramClass = $this->detectParamClass($reflection);

        if (null === $paramClass) {
            return [];
        }

        return ParamDocExtractor::extract($paramClass);
    }

    /**
     * 检测 execute 方法的参数类
     *
     * @return class-string|null
     */
    private function detectParamClass(\ReflectionClass $reflection): ?string
    {
        if (!$reflection->hasMethod('execute')) {
            return null;
        }

        $executeMethod = $reflection->getMethod('execute');
        $parameters = $executeMethod->getParameters();

        if ([] === $parameters) {
            return null;
        }

        $type = $parameters[0]->getType();
        if (null === $type) {
            return null;
        }

        if ($type instanceof \ReflectionUnionType) {
            return $this->findConcreteParamClassFromUnion($type);
        }

        return $this->resolveNamedType($type, $executeMethod);
    }

    /**
     * @return class-string|null
     */
    private function findConcreteParamClassFromUnion(\ReflectionUnionType $type): ?string
    {
        foreach ($type->getTypes() as $unionType) {
            if (!$unionType instanceof \ReflectionNamedType) {
                continue;
            }
            $typeName = $unionType->getName();
            if (RpcParamInterface::class !== $typeName
                && class_exists($typeName)
                && is_subclass_of($typeName, RpcParamInterface::class)) {
                return $typeName;
            }
        }

        return null;
    }

    /**
     * @return class-string|null
     */
    private function resolveNamedType(\ReflectionType $type, \ReflectionMethod $executeMethod): ?string
    {
        if (!$type instanceof \ReflectionNamedType) {
            return null;
        }

        $typeName = $type->getName();

        if (RpcParamInterface::class === $typeName) {
            return $this->detectParamClassFromDocBlock($executeMethod);
        }

        if (class_exists($typeName) && is_subclass_of($typeName, RpcParamInterface::class)) {
            return $typeName;
        }

        return null;
    }

    /**
     * 从 DocBlock 中提取参数类型
     *
     * @return class-string|null
     */
    private function detectParamClassFromDocBlock(\ReflectionMethod $method): ?string
    {
        $docComment = $method->getDocComment();

        if (false === $docComment) {
            return null;
        }

        // 匹配 @param ClassName $param 格式
        if (preg_match('/@param\s+([A-Za-z0-9_\\\]+)\s+\$\w+/', $docComment, $matches)) {
            $className = $matches[1];

            // 处理相对命名空间
            if (!str_starts_with($className, '\\')) {
                $declaringClass = $method->getDeclaringClass();
                $namespace = $declaringClass->getNamespaceName();

                // 检查是否有 use 语句
                $fullClassName = $this->resolveClassName($className, $declaringClass);
                if (null !== $fullClassName) {
                    $className = $fullClassName;
                } else {
                    // 尝试在同一命名空间下查找
                    $className = $namespace . '\\' . $className;
                }
            }

            $className = ltrim($className, '\\');

            if (class_exists($className) && is_subclass_of($className, RpcParamInterface::class)) {
                return $className;
            }
        }

        return null;
    }

    /**
     * 解析类名（处理 use 语句）
     *
     * @return class-string|null
     */
    private function resolveClassName(string $shortName, \ReflectionClass $context): ?string
    {
        $filename = $context->getFileName();

        if (false === $filename) {
            return null;
        }

        $content = file_get_contents($filename);
        if (false === $content) {
            return null;
        }

        // 匹配 use statements
        if (preg_match('/use\s+([A-Za-z0-9_\\\]+\\\\' . preg_quote($shortName, '/') . ')\s*;/', $content, $matches)) {
            return $matches[1];
        }

        // 匹配 use ... as ShortName
        if (preg_match('/use\s+([A-Za-z0-9_\\\]+)\s+as\s+' . preg_quote($shortName, '/') . '\s*;/', $content, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
