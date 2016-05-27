<?php

namespace Doppy\UtilBundle;

use Doppy\UtilBundle\DependencyInjection\CompilerPass\NullStopwatchCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DoppyUtilBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new NullStopwatchCompilerPass());
    }
}
