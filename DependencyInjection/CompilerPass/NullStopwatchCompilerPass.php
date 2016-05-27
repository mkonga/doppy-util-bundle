<?php

namespace Doppy\UtilBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class NullStopwatchCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('debug.stopwatch')) {
            $container->setAlias('debug.stopwatch', 'doppy_util.null_stopwatch');
        }
    }
}