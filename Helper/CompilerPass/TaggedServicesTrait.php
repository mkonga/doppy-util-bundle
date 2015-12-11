<?php

namespace Doppy\UtilBundle\Helper\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

trait TaggedServicesTrait
{
    protected function processTaggedServices(ContainerBuilder $container, $name, $method, $forceLazy = false) {
        // do nothing when the service is not found
        if (!$container->has($name)) {
            return;
        }

        // retrieve main service
        $serviceDefinition = $container->findDefinition($name);

        // retrieve tagged services
        $taggedServices = $container->findTaggedServiceIds($name);

        // prepare a resulting list
        $list = [];
        foreach ($taggedServices as $id => $tags) {
            // make lazy?
            if ($forceLazy) {
                $taggedService = $container->findDefinition($id);
                $taggedService->setLazy(true);
            }

            foreach ($tags as $attributes) {
                // determine the priority
                $priority = 0;
                if (isset($attributes['priority'])) {
                    $priority = $attributes['priority'];
                }

                // add it to list
                $list[] = array('id' => $id, 'priority' => $priority);
            }
        }

        // sort what we got
        usort($list, function($left, $right) {
            return $left['priority'] - $right['priority'];
        });

        // now add them in requested order
        foreach ($list as $taggedService) {
            // add the MenuBuilder to our repository
            $serviceDefinition->addMethodCall($method, array(new Reference($taggedService['id'])));
        }
    }
}