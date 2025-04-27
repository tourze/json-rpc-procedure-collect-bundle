<?php

namespace Tourze\JsonRPCProcedureCollectBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\JsonRPCProcedureCollectBundle\DependencyInjection\NameCollectorCompilerPass;

class JsonRPCProcedureCollectBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new NameCollectorCompilerPass());
    }
}
