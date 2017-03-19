<?php

namespace Doppy\UtilBundle\Helper\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

trait PrependConfigTrait
{
    /**
     * @param ContainerBuilder $container
     * @param string           $filename
     */
    protected function prependConfig(ContainerBuilder $container, $filename)
    {
        // get parser
        $yamlParser = new Parser();

        // parse config
        $prependConfig = $yamlParser->parse(file_get_contents($filename));

        // now add for each bundle
        foreach ($prependConfig as $bundleName => $bundleConfig) {
            $container->prependExtensionConfig($bundleName, $bundleConfig);
        }
    }
}
