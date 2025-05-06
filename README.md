# JSON-RPC过程收集模块

这个Symfony Bundle负责收集所有标记为JSON-RPC方法的服务，并提供一个API来查询所有可用的方法。

## 安装

```bash
composer require tourze/json-rpc-procedure-collect-bundle
```

## 注册Bundle

在`config/bundles.php`中添加：

```php
Tourze\JsonRPCProcedureCollectBundle\JsonRPCProcedureCollectBundle::class => ['all' => true],
```

## 功能介绍

本Bundle主要提供以下功能：

1. 自动收集所有使用`#[MethodExpose]`属性标记的JSON-RPC方法
2. 提供`GetProcedureList` JSON-RPC方法，用于获取所有已注册的方法列表

## 使用示例

### 创建JSON-RPC方法

```php
<?php

namespace App\Procedure;

use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

#[MethodExpose('YourMethodName')]
class YourMethod extends BaseProcedure
{
    public function execute(): array
    {
        // 实现你的方法逻辑
        return ['key' => 'value'];
    }
}
```

### 获取所有方法列表

通过调用`GetProcedureList` JSON-RPC方法，可以获取所有已注册的JSON-RPC方法及其对应的实现类：

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
    "YourMethodName": "App\\Procedure\\YourMethod"
  },
  "id": 1
}
```

## 单元测试

运行单元测试：

```bash
./vendor/bin/phpunit packages/json-rpc-procedure-collect-bundle/tests
```
