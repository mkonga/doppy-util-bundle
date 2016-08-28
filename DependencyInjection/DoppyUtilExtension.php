<?php

namespace Doppy\UtilBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class DoppyUtilExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $tempFilePath = $config['temp_file']['path'];
        if ($tempFilePath !== false) {
            if (!is_dir($tempFilePath)) {
                throw new \Exception(
                    sprintf('temp_file path ("%s") is not a directory', $tempFilePath)
                );
            }
            if (!is_writeable($tempFilePath)) {
                throw new \Exception(
                    sprintf('temp_file path is not writable', $tempFilePath)
                );
            }
        }
        $container->setParameter('doppy_util.temp_file.path', $config['temp_file']['path']);
        $container->setParameter('doppy_util.temp_file.cleanup_on_terminate', $config['temp_file']['cleanup_on_terminate']);
        $container->setParameter('doppy_util.nullstopwatch', $config['nullstopwatch']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        if ($config['temp_file']['cleanup_on_terminate']) {
            $loader->load('services/temp_file.cleanup.listener.yml');
        }
        if ($config['nullstopwatch']) {
            $loader->load('services/nullstopwatch.yml');
        }
    }
}
