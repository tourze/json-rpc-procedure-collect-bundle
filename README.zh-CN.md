# JSON-RPC 过程收集模块

[English](README.md) | [中文](README.zh-CN.md)

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue)](https://php.net)
[![Symfony Version](https://img.shields.io/badge/symfony-%3E%3D7.3-blue)](https://symfony.com)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen)](#)
[![Code Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen)](#)

这个 Symfony Bundle 负责收集所有标记为 JSON-RPC 方法的服务，并提供一个 API 来查询所有可用的方法。

## 功能特性

- 自动收集所有使用 `#[MethodExpose]` 属性标记的 JSON-RPC 方法
- 提供 `GetProcedureList` JSON-RPC 方法来获取所有已注册的方法
- 支持通过 `#[MethodDoc]` 和 `#[MethodTag]` 属性进行方法文档化和标记
- 依赖注入集成，实现自动服务发现

## 安装

```bash
composer require tourze/json-rpc-procedure-collect-bundle
```

## Bundle 注册

在 `config/bundles.php` 中添加：

```php
Tourze\JsonRPCProcedureCollectBundle\JsonRPCProcedureCollectBundle::class => ['all' => true],
```

## 快速开始

### 创建 JSON-RPC 方法

```php
<?php

namespace App\Procedure;

use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

#[MethodTag(name: '用户服务')]
#[MethodDoc(summary: '获取用户信息')]
#[MethodExpose(method: 'GetUserInfo')]
class GetUserInfo extends BaseProcedure
{
    public function execute(): array
    {
        // 实现你的方法逻辑
        return ['user' => 'data'];
    }
}
```

### 获取所有可用方法

通过调用 `GetProcedureList` JSON-RPC 方法，可以获取所有已注册的 JSON-RPC 方法及其对应的实现类：

```json
{
  "jsonrpc": "2.0",
  "method": "GetProcedureList",
  "params": {},
  "id": 1
}
```

响应示例：

```json
{
  "jsonrpc": "2.0",
  "result": {
    "GetProcedureList": "Tourze\\JsonRPCProcedureCollectBundle\\Procedure\\GetProcedureList",
    "GetUserInfo": "App\\Procedure\\GetUserInfo"
  },
  "id": 1
}
```

## 系统要求

- PHP >= 8.1
- Symfony >= 7.3
- `tourze/json-rpc-core` 包

## 测试

运行测试套件：

```bash
./vendor/bin/phpunit packages/json-rpc-procedure-collect-bundle/tests
```

## 贡献

请查看 [CONTRIBUTING.md](CONTRIBUTING.md) 了解详情。

## 许可证

MIT 许可证。请查看 [License File](LICENSE) 了解更多信息。
