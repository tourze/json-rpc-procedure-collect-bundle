# JSON-RPC Procedure Collect Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue)](https://php.net)
[![Symfony Version](https://img.shields.io/badge/symfony-%3E%3D7.3-blue)](https://symfony.com)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen)](#)
[![Code Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen)](#)

A Symfony Bundle that collects all services marked as JSON-RPC methods and provides an API to query all available methods.

## Features

- Automatically collect all JSON-RPC methods marked with `#[MethodExpose]` attribute
- Provide `GetProcedureList` JSON-RPC method to retrieve all registered methods
- Support for method documentation and tagging via `#[MethodDoc]` and `#[MethodTag]` attributes
- Dependency injection integration for automatic service discovery

## Installation

```bash
composer require tourze/json-rpc-procedure-collect-bundle
```

## Bundle Registration

Add to `config/bundles.php`:

```php
Tourze\JsonRPCProcedureCollectBundle\JsonRPCProcedureCollectBundle::class => ['all' => true],
```

## Quick Start

### Creating a JSON-RPC Method

```php
<?php

namespace App\Procedure;

use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

#[MethodTag(name: 'User Service')]
#[MethodDoc(summary: 'Get user information')]
#[MethodExpose(method: 'GetUserInfo')]
class GetUserInfo extends BaseProcedure
{
    public function execute(): array
    {
        // Implement your method logic
        return ['user' => 'data'];
    }
}
```

### Retrieving All Available Methods

Call the `GetProcedureList` JSON-RPC method to get all registered JSON-RPC methods and their corresponding implementation classes:

```json
{
  "jsonrpc": "2.0",
  "method": "GetProcedureList",
  "params": {},
  "id": 1
}
```

Response example:

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

## Requirements

- PHP >= 8.1
- Symfony >= 7.3
- `tourze/json-rpc-core` package

## Testing

Run the test suite:

```bash
./vendor/bin/phpunit packages/json-rpc-procedure-collect-bundle/tests
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
